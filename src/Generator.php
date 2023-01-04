<?php

namespace ApiClients\Tools\OpenApiClientGenerator;

use ApiClients\Tools\OpenApiClientGenerator\Generator\Client;
use ApiClients\Tools\OpenApiClientGenerator\Generator\Clients;
use ApiClients\Tools\OpenApiClientGenerator\Generator\Operation;
use ApiClients\Tools\OpenApiClientGenerator\Generator\Path;
use ApiClients\Tools\OpenApiClientGenerator\Generator\Schema;
use ApiClients\Tools\OpenApiClientGenerator\Generator\WebHook;
use ApiClients\Tools\OpenApiClientGenerator\Generator\WebHookInterface;
use ApiClients\Tools\OpenApiClientGenerator\Generator\WebHooks;
use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use Jawira\CaseConverter\Convert;
use PhpParser\PrettyPrinter\Standard;

final class Generator
{
    private OpenApi $spec;

    public function __construct(string $specUrl)
    {
        /** @var OpenApi spec */
        $this->spec = Reader::readFromYamlFile($specUrl);
    }

    public function generate(string $namespace, string $destinationPath)
    {
        $namespace = self::cleanUpNamespace($namespace);
        $codePrinter = new Standard();

        foreach ($this->all($namespace) as $file) {
            $fileName = $destinationPath . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, substr($file->fqcn(), strlen($namespace)));
            @mkdir(dirname($fileName), 0744, true);
            file_put_contents($fileName . '.php', $codePrinter->prettyPrintFile([$file->contents()]) . PHP_EOL);
        }
    }

    public static function className(string $className): string
    {
        return str_replace(['{', '}', '-', '$', '_'], ['Cb', 'Rcb', 'Dash', '_', '\\'], (new Convert($className))->toPascal()) . (self::isKeyword($className) ? '_' : '');
    }

    private static function cleanUpNamespace(string $namespace): string
    {
        $namespace = str_replace('/', '\\', $namespace);
        $namespace = str_replace('\\\\', '\\', $namespace);

        return $namespace;
    }

    /**
     * @param string $namespace
     * @param string $destinationPath
     * @return iterable<File>
     */
    private function all(string $namespace): iterable
    {
        $schemaRegistry = new SchemaRegistry();
        if (count($this->spec->components->schemas ?? []) > 0) {
            foreach ($this->spec->components->schemas as $name => $schema) {
                $schemaClassName = self::className($name);
                if (strlen($schemaClassName) === 0) {
                    continue;
                }
                $schemaRegistry->addClassName($schemaClassName, $schema);
            }
            foreach ($this->spec->components->schemas as $name => $schema) {
                $schemaClassName = $schemaRegistry->get($schema);
                if (strlen($schemaClassName) === 0) {
                    continue;
                }

                yield from Schema::generate(
                    $name,
                    self::dirname($namespace . 'Schema/' . $schemaClassName),
                    self::basename($namespace . 'Schema/' . $schemaClassName),
                    $schema,
                    $schemaRegistry,
                    $namespace . 'Schema'
                );
            }
        }

        $clients = [];
        if (count($this->spec->paths ?? []) > 0) {
            foreach ($this->spec->paths as $path => $pathItem) {
                $pathClassName = self::className($path);
                if (strlen($pathClassName) === 0) {
                    continue;
                }

                yield from Path::generate(
                    $path,
                    self::dirname($namespace . 'Path/' . $pathClassName),
                    $namespace,
                    self::basename($namespace . 'Path/' . $pathClassName),
                    $pathItem
                );

                foreach ($pathItem->getOperations() as $method => $operation) {
                    $operationClassName = self::className((new Convert($operation->operationId))->fromTrain()->toPascal());
                    $operations[$method] = $operationClassName;
                    if (strlen($operationClassName) === 0) {
                        continue;
                    }

                    yield from Operation::generate(
                        $path,
                        $method,
                        self::dirname($namespace . 'Operation/' . $operationClassName),
                        $namespace,
                        self::basename($namespace . 'Operation/' . $operationClassName),
                        $operation,
                        $schemaRegistry
                    );

                    [$operationGroup, $operationOperation] = explode('/', $operationClassName);
                    if (!array_key_exists($operationGroup, $clients)) {
                        $clients[$operationGroup] = [];
                    }
                    $clients[$operationGroup][$operationOperation] = [
                        'class' => $operationClassName,
                        'operation' => $operation,
                    ];
                }
            }
        }

        yield from (function (array $clients, string $namespace, SchemaRegistry $schemaRegistry): \Generator {
            foreach ($clients as $operationGroup => $operations) {
                yield from Client::generate(
                    $operationGroup,
                    self::dirname($namespace . 'Operation/' . $operationGroup),
                    self::basename($namespace . 'Operation/' . $operationGroup),
                    $operations,
                );

            }
            yield from Clients::generate(
                $namespace,
                $clients,
                $schemaRegistry,
            );
        })($clients, $namespace, $schemaRegistry);

        if (count($this->spec->webhooks ?? []) > 0) {
            $pathClassNameMapping = [];
            foreach ($this->spec->webhooks as $path => $pathItem) {
                $webHookClassName = self::className($path);
                $pathClassNameMapping[$path] = $this->fqcn($namespace . 'WebHook/' . $webHookClassName);
                if (strlen($webHookClassName) === 0) {
                    continue;
                }

                yield from WebHook::generate(
                    $path,
                    self::dirname($namespace . 'WebHook/' . $webHookClassName),
                    $namespace,
                    self::basename($namespace . 'WebHook/' . $webHookClassName),
                    $pathItem,
                    $schemaRegistry,
                    $namespace
                );
            }

            yield from WebHookInterface::generate(
                self::dirname($namespace . 'WebHookInterface'),
                'WebHookInterface',
            );
            yield from WebHooks::generate(
                self::dirname($namespace . 'WebHooks'),
                $namespace,
                $pathClassNameMapping,
            );
        }

        while ($schemaRegistry->hasUnknownSchemas()) {
            foreach ($schemaRegistry->unknownSchemas() as $schema) {
                yield from Schema::generate(
                    $schema['name'],
                    self::dirname($namespace . 'Schema/' . $schema['className']),
                    self::basename($namespace . 'Schema/' . $schema['className']),
                    $schema['schema'],
                    $schemaRegistry,
                    $namespace . 'Schema'
                );
            }
        }
    }

    private static function fqcn(string $fqcn): string
    {
        return str_replace('/', '\\', $fqcn);
    }

    public static function dirname(string $fqcn): string
    {
        $fqcn = str_replace('\\', '/', $fqcn);

        return self::cleanUpNamespace(dirname($fqcn));
    }

    public static function basename(string $fqcn): string
    {
        $fqcn = str_replace('\\', '/', $fqcn);

        return self::cleanUpNamespace(basename($fqcn));
    }

    private static function isKeyword(string $name): bool
    {
        return in_array(strtolower($name), array('__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor', 'self'), false);
    }
}
