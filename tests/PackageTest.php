<?php

declare(strict_types=1);

namespace OpenAPITools\Tests\Configuration;

use OpenAPITools\Configuration\Package;
use OpenAPITools\Configuration\Package\Destination;
use OpenAPITools\Configuration\Package\Metadata;
use OpenAPITools\Configuration\Package\QA;
use OpenAPITools\Configuration\Package\QA\Tool;
use OpenAPITools\Configuration\Package\State as PackageState;
use OpenAPITools\Configuration\Package\Templates;
use OpenAPITools\Utils\Namespace_;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

final class PackageTest extends TestCase
{
    #[Test]
    public function constructWithAllOptionalsSet(): void
    {
        $metadata    = new Metadata('GitHub', 'GitHub API client', ['github']);
        $templates   = new Templates(__DIR__ . '/templates', ['key' => 'value']);
        $destination = new Destination('github', 'src', 'tests');
        $namespace   = new Namespace_(
            'ApiClients\Client\GitHub',
            'ApiClients\Tests\Client\GitHub',
        );
        $qa          = new QA(
            new Tool(true, null),
            new Tool(true, 'etc/phpstan-extension.neon'),
            new Tool(false, null),
        );
        $state       = new PackageState(['composer.json']);
        $generators  = [];

        $package = new Package(
            $metadata,
            'api-clients',
            'github',
            'git@github.com:php-api-clients/github.git',
            'v0.2.x',
            '1.0.0',
            $templates,
            $destination,
            $namespace,
            $qa,
            $state,
            $generators,
        );

        self::assertSame($metadata, $package->metadata);
        self::assertSame('api-clients', $package->vendor);
        self::assertSame('github', $package->name);
        self::assertSame('git@github.com:php-api-clients/github.git', $package->repository);
        self::assertSame('v0.2.x', $package->branch);
        self::assertSame('1.0.0', $package->targetVersion);
        self::assertSame($templates, $package->templates);
        self::assertSame($destination, $package->destination);
        self::assertSame($namespace, $package->namespace);
        self::assertSame($qa, $package->qa);
        self::assertSame($state, $package->state);
        self::assertSame($generators, $package->generators);
    }

    #[Test]
    public function constructWithNullOptionals(): void
    {
        $metadata    = new Metadata('Example', 'Example package', []);
        $destination = new Destination('root', 'src', 'tests');
        $namespace   = new Namespace_('Vendor\\Package', 'Vendor\\Tests\\Package');
        $qa          = new QA(null, null, null);
        $state       = new PackageState(null);

        $package = new Package(
            $metadata,
            'vendor',
            'package',
            null,
            null,
            null,
            null,
            $destination,
            $namespace,
            $qa,
            $state,
            [],
        );

        self::assertSame($metadata, $package->metadata);
        self::assertSame('vendor', $package->vendor);
        self::assertSame('package', $package->name);
        self::assertNull($package->repository);
        self::assertNull($package->branch);
        self::assertNull($package->targetVersion);
        self::assertNull($package->templates);
        self::assertSame($destination, $package->destination);
        self::assertSame($namespace, $package->namespace);
        self::assertSame($qa, $package->qa);
        self::assertSame($state, $package->state);
        self::assertSame([], $package->generators);
    }
}
