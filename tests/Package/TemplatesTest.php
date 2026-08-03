<?php

declare(strict_types=1);

namespace OpenAPITools\Tests\Configuration\Package;

use OpenAPITools\Configuration\Package\Templates;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

final class TemplatesTest extends TestCase
{
    /** @return iterable<string, array{string, array<string, mixed>|null}> */
    public static function templatesProvider(): iterable
    {
        yield 'empty variables' => [__DIR__ . '/../../templates', []];
        yield 'null variables' => [__DIR__ . '/../../templates', null];
        yield 'with variables' => [
            '/path/to/templates',
            ['key' => 'value', 'nested' => ['a' => 1]],
        ];
    }

    /** @param array<string, mixed>|null $variables */
    #[Test]
    #[DataProvider('templatesProvider')]
    public function construct(string $dir, array|null $variables): void
    {
        $templates = new Templates($dir, $variables);

        self::assertSame($dir, $templates->dir);
        self::assertSame($variables, $templates->variables);
    }
}
