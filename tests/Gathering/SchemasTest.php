<?php

declare(strict_types=1);

namespace OpenAPITools\Tests\Configuration\Gathering;

use EventSauce\ObjectHydrator\ObjectMapperUsingReflection;
use OpenAPITools\Configuration\Gathering\Schemas;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

final class SchemasTest extends TestCase
{
    /** @return iterable<string, array{bool, bool}> */
    public static function flagsProvider(): iterable
    {
        yield 'both true' => [true, true];
        yield 'both false' => [false, false];
        yield 'allow only' => [true, false];
        yield 'aliases only' => [false, true];
    }

    #[Test]
    #[DataProvider('flagsProvider')]
    public function construct(bool $allowDuplication, bool $useAliasesForDuplication): void
    {
        $schemas = new Schemas($allowDuplication, $useAliasesForDuplication);

        self::assertSame($allowDuplication, $schemas->allowDuplication);
        self::assertSame($useAliasesForDuplication, $schemas->useAliasesForDuplication);
    }

    #[Test]
    #[DataProvider('flagsProvider')]
    public function hydrateFromCamelCaseKeys(bool $allowDuplication, bool $useAliasesForDuplication): void
    {
        $schemas = new ObjectMapperUsingReflection()->hydrateObject(
            Schemas::class,
            [
                'allowDuplication' => $allowDuplication,
                'useAliasesForDuplication' => $useAliasesForDuplication,
            ],
        );

        self::assertSame($allowDuplication, $schemas->allowDuplication);
        self::assertSame($useAliasesForDuplication, $schemas->useAliasesForDuplication);
    }
}
