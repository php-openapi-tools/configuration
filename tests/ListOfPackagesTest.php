<?php

declare(strict_types=1);

namespace OpenAPITools\Tests\Configuration;

use OpenAPITools\Configuration\Configuration;
use OpenAPITools\Configuration\Gathering;
use OpenAPITools\Configuration\ListOfPackages;
use OpenAPITools\Configuration\Package\QA;
use OpenAPITools\Configuration\Package\State as PackageState;
use OpenAPITools\Configuration\Package\Templates;
use OpenAPITools\Configuration\State;
use OpenAPITools\Representation\Client;
use OpenAPITools\Representation\Representation;
use OpenAPITools\Utils\Namespace_;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

use function iterator_to_array;

final class ListOfPackagesTest extends TestCase
{
    #[Test]
    public function listYieldsNoPackages(): void
    {
        $listOfPackages = new ListOfPackages(
            'api-clients',
            'all',
            'git@github.com:example/all.git',
            'main',
            '1.0.0',
            new Templates(__DIR__ . '/templates', []),
            new Namespace_(
                'ApiClients\Client\GitHub',
                'ApiClients\Tests\Client\GitHub',
            ),
            new QA(null, null, null),
            new PackageState(['composer.json']),
            [],
        );

        $packages = iterator_to_array(
            $listOfPackages->list(
                new Configuration(
                    new State('state.json'),
                    new Gathering('spec.yaml', null, null),
                    [],
                ),
                new Representation(new Client(null, []), [], []),
            ),
            false,
        );

        self::assertSame([], $packages);
    }
}
