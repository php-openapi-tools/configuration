<?php

declare(strict_types=1);

namespace OpenAPITools\Tests\Configuration;

use OpenAPITools\Configuration\State;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

final class StateTest extends TestCase
{
    /** @return iterable<string, array{string}> */
    public static function fileProvider(): iterable
    {
        yield 'default state file' => ['state.json'];
        yield 'nested path' => ['etc/state.json'];
        yield 'absolute path' => ['/var/state/state.json'];
    }

    #[Test]
    #[DataProvider('fileProvider')]
    public function construct(string $file): void
    {
        $state = new State($file);

        self::assertSame($file, $state->file);
    }
}
