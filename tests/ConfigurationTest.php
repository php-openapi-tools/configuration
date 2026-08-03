<?php

declare(strict_types=1);

namespace OpenAPITools\Tests\Configuration;

use EventSauce\ObjectHydrator\ObjectMapperUsingReflection;
use OpenAPITools\Configuration\Configuration;
use OpenAPITools\Configuration\Gathering;
use OpenAPITools\Configuration\ListOfPackages;
use OpenAPITools\Configuration\Package;
use OpenAPITools\Configuration\Package\Destination;
use OpenAPITools\Configuration\Package\Metadata;
use OpenAPITools\Configuration\Package\QA;
use OpenAPITools\Configuration\Package\QA\Tool;
use OpenAPITools\Configuration\Package\State as PackageState;
use OpenAPITools\Configuration\Package\Templates;
use OpenAPITools\Configuration\State;
use OpenAPITools\Utils\Namespace_;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

final class ConfigurationTest extends TestCase
{
    #[Test]
    public function construct(): void
    {
        $state         = new State('etc/state.json');
        $gathering     = new Gathering(
            'spec.yaml',
            null,
            new Gathering\Schemas(true, true),
        );
        $package       = new Package(
            new Metadata('GitHub', 'GitHub API client', ['github']),
            'api-clients',
            'github',
            'git@github.com:php-api-clients/github.git',
            'v0.2.x',
            null,
            new Templates(__DIR__ . '/templates', []),
            new Destination('github', 'src', 'tests'),
            new Namespace_(
                'ApiClients\Client\GitHub',
                'ApiClients\Tests\Client\GitHub',
            ),
            new QA(
                new Tool(true, null),
                new Tool(true, 'etc/phpstan-extension.neon'),
                new Tool(false, null),
            ),
            new PackageState(['composer.json']),
            [],
        );
        $listOf        = new ListOfPackages(
            'api-clients',
            'all',
            null,
            null,
            null,
            null,
            new Namespace_(
                'ApiClients\Client\GitHub',
                'ApiClients\Tests\Client\GitHub',
            ),
            new QA(null, null, null),
            new PackageState(null),
            [],
        );
        $configuration = new Configuration(
            $state,
            $gathering,
            [$package, $listOf],
        );

        self::assertSame($state, $configuration->state);
        self::assertSame($gathering, $configuration->gathering);
        self::assertCount(2, $configuration->packages);
        self::assertInstanceOf(Package::class, $configuration->packages[0]);
        self::assertInstanceOf(ListOfPackages::class, $configuration->packages[1]);
    }

    #[Test]
    public function hydrateFromArray(): void
    {
        $configuration = new ObjectMapperUsingReflection()->hydrateObject(
            Configuration::class,
            [
                'state' => ['file' => 'state.json'],
                'gathering' => [
                    'spec' => 'api.github.com.yaml',
                    'voter' => null,
                    'schemas' => [
                        'allowDuplication' => false,
                        'useAliasesForDuplication' => true,
                    ],
                ],
                'packages' => [],
            ],
        );

        self::assertSame('state.json', $configuration->state->file);
        self::assertSame('api.github.com.yaml', $configuration->gathering->spec);
        self::assertNull($configuration->gathering->voter);
        self::assertNotNull($configuration->gathering->schemas);
        self::assertFalse($configuration->gathering->schemas->allowDuplication);
        self::assertTrue($configuration->gathering->schemas->useAliasesForDuplication);
        self::assertSame([], $configuration->packages);
    }
}
