<?php

declare(strict_types=1);

namespace OpenAPITools\Tests\Configuration\Package;

use OpenAPITools\Configuration\Package\QA;
use OpenAPITools\Configuration\Package\QA\Tool;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

final class QATest extends TestCase
{
    /** @return iterable<string, array{Tool|null, Tool|null, Tool|null}> */
    public static function toolsProvider(): iterable
    {
        yield 'all tools' => [
            new Tool(true, null),
            new Tool(true, 'phpstan.neon'),
            new Tool(false, null),
        ];

        yield 'all null' => [null, null, null];

        yield 'phpcs only' => [
            new Tool(true, 'phpcs.xml'),
            null,
            null,
        ];
    }

    #[Test]
    #[DataProvider('toolsProvider')]
    public function construct(Tool|null $phpcs, Tool|null $phpstan, Tool|null $psalm): void
    {
        $qa = new QA($phpcs, $phpstan, $psalm);

        self::assertSame($phpcs, $qa->phpcs);
        self::assertSame($phpstan, $qa->phpstan);
        self::assertSame($psalm, $qa->psalm);
    }
}
