<?php

declare(strict_types=1);

namespace OpenAPITools\Tests\Configuration\Gathering;

use EventSauce\ObjectHydrator\ObjectMapperUsingReflection;
use OpenAPITools\Configuration\Gathering\Voter;
use OpenAPITools\Contract\Voter\ListOperation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

final class VoterTest extends TestCase
{
    /** @return iterable<string, array{array<class-string<ListOperation>>|null}> */
    public static function listOperationProvider(): iterable
    {
        yield 'null' => [null];
        yield 'list operation' => [[ListOperation::class]];
        yield 'empty list' => [[]];
    }

    /** @param array<class-string<ListOperation>>|null $listOperation */
    #[Test]
    #[DataProvider('listOperationProvider')]
    public function construct(array|null $listOperation): void
    {
        $voter = new Voter($listOperation, null);

        self::assertSame($listOperation, $voter->listOperation);
        self::assertNull($voter->streamOperation);
    }

    /** @param array<class-string<ListOperation>>|null $listOperation */
    #[Test]
    #[DataProvider('listOperationProvider')]
    public function hydrateListOperationFromCamelCaseKey(array|null $listOperation): void
    {
        $voter = new ObjectMapperUsingReflection()->hydrateObject(
            Voter::class,
            [
                'listOperation' => $listOperation,
                'streamOperation' => null,
            ],
        );

        self::assertSame($listOperation, $voter->listOperation);
        self::assertNull($voter->streamOperation);
    }

    #[Test]
    public function constructWithStreamOperationClassNames(): void
    {
        $streamOperation = ['OpenAPITools\\Voter\\StreamOperation'];
        $voter           = new Voter(null, $streamOperation);

        self::assertNull($voter->listOperation);
        self::assertSame($streamOperation, $voter->streamOperation);
    }

    #[Test]
    public function hydrateStreamOperationFromCamelCaseKey(): void
    {
        $streamOperation = ['OpenAPITools\\Voter\\StreamOperation'];
        $voter           = new ObjectMapperUsingReflection()->hydrateObject(
            Voter::class,
            [
                'listOperation' => null,
                'streamOperation' => $streamOperation,
            ],
        );

        self::assertNull($voter->listOperation);
        self::assertSame($streamOperation, $voter->streamOperation);
    }
}
