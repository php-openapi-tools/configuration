<?php

declare(strict_types=1);

namespace OpenAPITools\Tests\Configuration\Package;

use OpenAPITools\Configuration\Package\Metadata;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

final class MetadataTest extends TestCase
{
    /** @return iterable<string, array{string, string, array<string>}> */
    public static function metadataProvider(): iterable
    {
        yield 'with keywords' => [
            'GitHub',
            'GitHub API client',
            ['github', 'api', 'rest'],
        ];

        yield 'empty keywords' => [
            'Example',
            'Example package',
            [],
        ];
    }

    /** @param array<string> $keywords */
    #[Test]
    #[DataProvider('metadataProvider')]
    public function construct(string $name, string $description, array $keywords): void
    {
        $metadata = new Metadata($name, $description, $keywords);

        self::assertSame($name, $metadata->name);
        self::assertSame($description, $metadata->description);
        self::assertSame($keywords, $metadata->keywords);
    }
}
