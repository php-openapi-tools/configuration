<?php

declare(strict_types=1);

namespace OpenAPITools\Tests\Configuration\Package\QA;

use EventSauce\ObjectHydrator\ObjectMapperUsingReflection;
use OpenAPITools\Configuration\Package\QA\Tool;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

final class ToolTest extends TestCase
{
    /** @return iterable<string, array{bool, string|null}> */
    public static function toolProvider(): iterable
    {
        yield 'enabled without config' => [true, null];
        yield 'disabled without config' => [false, null];
        yield 'enabled with config' => [true, 'etc/qa/phpstan.neon'];
        yield 'disabled with config' => [false, 'etc/qa/phpcs.xml'];
    }

    #[Test]
    #[DataProvider('toolProvider')]
    public function construct(bool $enabled, string|null $configFilePath): void
    {
        $tool = new Tool($enabled, $configFilePath);

        self::assertSame($enabled, $tool->enabled);
        self::assertSame($configFilePath, $tool->configFilePath);
    }

    #[Test]
    #[DataProvider('toolProvider')]
    public function hydrateFromCamelCaseKey(bool $enabled, string|null $configFilePath): void
    {
        $tool = new ObjectMapperUsingReflection()->hydrateObject(
            Tool::class,
            [
                'enabled' => $enabled,
                'configFilePath' => $configFilePath,
            ],
        );

        self::assertSame($enabled, $tool->enabled);
        self::assertSame($configFilePath, $tool->configFilePath);
    }
}
