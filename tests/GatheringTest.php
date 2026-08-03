<?php

declare(strict_types=1);

namespace OpenAPITools\Tests\Configuration;

use OpenAPITools\Configuration\Gathering;
use OpenAPITools\Configuration\Gathering\Schemas;
use OpenAPITools\Configuration\Gathering\Voter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

final class GatheringTest extends TestCase
{
    /** @return iterable<string, array{string, Gathering\Voter|null, Gathering\Schemas|null}> */
    public static function gatheringProvider(): iterable
    {
        yield 'all present' => [
            'openapi.yaml',
            new Gathering\Voter(null, null),
            new Gathering\Schemas(true, false),
        ];

        yield 'null voter' => [
            'openapi.yaml',
            null,
            new Gathering\Schemas(false, true),
        ];

        yield 'null schemas' => [
            'openapi.yaml',
            new Gathering\Voter([], null),
            null,
        ];

        yield 'all null' => [
            'openapi.yaml',
            null,
            null,
        ];
    }

    #[Test]
    #[DataProvider('gatheringProvider')]
    public function construct(string $spec, Voter|null $voter, Schemas|null $schemas): void
    {
        $gathering = new Gathering($spec, $voter, $schemas);

        self::assertSame($spec, $gathering->spec);
        self::assertSame($voter, $gathering->voter);
        self::assertSame($schemas, $gathering->schemas);
    }
}
