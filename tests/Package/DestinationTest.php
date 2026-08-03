<?php

declare(strict_types=1);

namespace OpenAPITools\Tests\Configuration\Package;

use OpenAPITools\Configuration\Package\Destination;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

final class DestinationTest extends TestCase
{
    /** @return iterable<string, array{string, string, string}> */
    public static function pathsProvider(): iterable
    {
        yield 'standard layout' => ['github', 'src', 'tests'];
        yield 'nested root' => ['packages/client', 'lib', 'spec'];
        yield 'single segment' => ['pkg', 'source', 'test'];
    }

    #[Test]
    #[DataProvider('pathsProvider')]
    public function construct(string $root, string $source, string $test): void
    {
        $destination = new Destination($root, $source, $test);

        self::assertSame($root, $destination->root);
        self::assertSame($source, $destination->source);
        self::assertSame($test, $destination->test);
    }
}
