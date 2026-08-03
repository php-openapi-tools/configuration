<?php

declare(strict_types=1);

namespace OpenAPITools\Tests\Configuration\Package;

use EventSauce\ObjectHydrator\ObjectMapperUsingReflection;
use OpenAPITools\Configuration\Package\State;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

final class PackageStateTest extends TestCase
{
    /** @return iterable<string, array{array<string>|null}> */
    public static function additionalFilesProvider(): iterable
    {
        yield 'null' => [null];
        yield 'empty list' => [[]];
        yield 'single file' => [['composer.json']];
        yield 'multiple files' => [['composer.json', 'composer.lock', 'README.md']];
    }

    /** @param array<string>|null $additionalFiles */
    #[Test]
    #[DataProvider('additionalFilesProvider')]
    public function construct(array|null $additionalFiles): void
    {
        $state = new State($additionalFiles);

        self::assertSame($additionalFiles, $state->additionalFiles);
    }

    /** @param array<string>|null $additionalFiles */
    #[Test]
    #[DataProvider('additionalFilesProvider')]
    public function hydrateFromCamelCaseKey(array|null $additionalFiles): void
    {
        $state = new ObjectMapperUsingReflection()->hydrateObject(
            State::class,
            ['additionalFiles' => $additionalFiles],
        );

        self::assertSame($additionalFiles, $state->additionalFiles);
    }
}
