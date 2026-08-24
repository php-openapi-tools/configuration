# configuration

Configuration value objects for [OpenAPI Tools](https://github.com/php-openapi-tools) package generators.

![Continuous Integration](https://github.com/php-openapi-tools/configuration/workflows/Continuous%20Integration/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/openapi-tools/configuration/v/stable.png)](https://packagist.org/packages/openapi-tools/configuration)
[![Total Downloads](https://poser.pugx.org/openapi-tools/configuration/downloads.png)](https://packagist.org/packages/openapi-tools/configuration/stats)
[![License](https://poser.pugx.org/openapi-tools/configuration/license.png)](https://packagist.org/packages/openapi-tools/configuration)

## Installation

To install via [Composer](https://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `^`.

```
composer require openapi-tools/configuration
```

## Components

| Class | Purpose |
| --- | --- |
| `Configuration` | Root value object holding state, gathering settings, and packages |
| `State` | Path to the global generation state file |
| `Gathering` | OpenAPI spec location and gathering options |
| `Gathering\Voter` | Optional list and stream operation voter class names |
| `Gathering\Schemas` | Schema duplication and aliasing settings |
| `Package` | Single generated package definition implementing `Contract\Package` |
| `ListOfPackages` | Expands into multiple `Package` instances at generation time |
| `Package\Metadata` | Composer package name, description, and keywords |
| `Package\Destination` | Output directory layout for source and tests |
| `Package\Templates` | Template directory and optional variables |
| `Package\QA` | QA tool configuration for phpcs, phpstan, and psalm |
| `Package\QA\Tool` | Enable flag and optional config file path for a QA tool |
| `Package\State` | Per-package state, including additional files to preserve |

## Usage

### Root configuration

Build a configuration in PHP or hydrate it from an array with [EventSauce Object Hydrator](https://github.com/EventSaucePHP/ObjectHydrator):

```php
use EventSauce\ObjectHydrator\ObjectMapperUsingReflection;
use OpenAPITools\Configuration\Configuration;

$configuration = new ObjectMapperUsingReflection()->hydrateObject(
    Configuration::class,
    [
        'state' => ['file' => 'state.json'],
        'gathering' => [
            'spec' => 'api.github.com.yaml',
            'voter' => null,
            'schemas' => [
                'allowDuplication' => false,
                'useAliasesForDuplication' => true,
            ],
        ],
        'packages' => [],
    ],
);
```

The `packages` key accepts a mix of `Package` and `ListOfPackages` entries. Generators such as `OpenAPITools\Generator\Generator` iterate this list and run file generators for each resolved package.

### YAML configuration

Multi-package setups typically use YAML. Paths are relative to the configuration directory:

```yaml
state:
  file: state.json
gathering:
  spec: https://raw.githubusercontent.com/github/rest-api-description/main/descriptions-next/api.github.com/api.github.com.yaml
  voter: []
  schemas:
    allowDuplication: true
    useAliasesForDuplication: true
packages:
  - vendor: api-clients
    name: github
    repository: git@github.com:php-api-clients/github.git
    branch: v0.2.x
    targetVersion: null
    templates:
      dir: ../templates
      variables: []
    namespace:
      source: ApiClients\Client\GitHub
      test: ApiClients\Tests\Client\GitHub
    qa:
      phpcs:
        enabled: true
      phpstan:
        enabled: true
        configFilePath: etc/phpstan-extension.neon
      psalm:
        enabled: false
    state:
      additionalFiles:
        - composer.json
        - composer.lock
```

Parse the YAML into an array, then hydrate `Configuration` or construct the value objects directly in PHP.

### Package definition

Each `Package` describes one generated client library: where files are written, which namespace to use, which QA tools to run, and which file generators to invoke:

```php
use OpenAPITools\Configuration\Package;
use OpenAPITools\Configuration\Package\Destination;
use OpenAPITools\Configuration\Package\Metadata;
use OpenAPITools\Configuration\Package\QA;
use OpenAPITools\Configuration\Package\QA\Tool;
use OpenAPITools\Configuration\Package\State as PackageState;
use OpenAPITools\Configuration\Package\Templates;
use OpenAPITools\Generator\Schema\Schema;
use OpenAPITools\Utils\Namespace_;
use PhpParser\BuilderFactory;

$builderFactory = new BuilderFactory();

$package = new Package(
    new Metadata('GitHub', 'GitHub API client', ['github']),
    'api-clients',
    'github',
    'git@github.com:php-api-clients/github.git',
    'v0.2.x',
    null,
    new Templates(__DIR__ . '/templates', []),
    new Destination('github', 'src', 'tests'),
    new Namespace_(
        'ApiClients\Client\GitHub',
        'ApiClients\Tests\Client\GitHub',
    ),
    new QA(
        new Tool(true, null),
        new Tool(true, 'etc/phpstan-extension.neon'),
        new Tool(false, null),
    ),
    new PackageState(['composer.json']),
    [
        new Schema($builderFactory),
    ],
);
```

`Package` implements `OpenAPITools\Contract\Package`. File generators attached via the `generators` property receive a namespaced `Representation` and yield `OpenAPITools\Utils\File` instances.

### Gathering options

`Gathering` controls how the OpenAPI spec is loaded and how the gatherer resolves schemas and operations:

```php
use OpenAPITools\Configuration\Gathering;
use OpenAPITools\Configuration\Gathering\Schemas;
use OpenAPITools\Configuration\Gathering\Voter;
use OpenAPITools\Gatherer\Gatherer;

$gathering = new Gathering(
    'api.github.com.yaml',
    new Voter(
        listOperation: [SomeListOperationVoter::class],
        streamOperation: ['streamOperationId'],
    ),
    new Schemas(
        allowDuplication: true,
        useAliasesForDuplication: true,
    ),
);

$representation = Gatherer::gather($openApi, $gathering);
```

The `spec` value may be a local path or URL. When `schemas` is omitted, gatherer defaults apply. When `voter` is omitted, no custom voters are registered.

### Dynamic package lists

`ListOfPackages` shares the same constructor shape as `Package` (without metadata and destination) and expands into concrete packages when generation runs:

```php
use OpenAPITools\Configuration\ListOfPackages;

$listOfPackages = new ListOfPackages(
    'api-clients',
    'all',
    null,
    null,
    null,
    null,
    new Namespace_(
        'ApiClients\Client\GitHub',
        'ApiClients\Tests\Client\GitHub',
    ),
    new QA(null, null, null),
    new PackageState(['composer.json']),
    [],
);

foreach ($listOfPackages->list($configuration, $representation) as $package) {
    // generate each resolved package
}
```

Use this when one configuration entry should produce many packages, for example when generating a client per API version or per spec section.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT)

Copyright (c) 2026 Cees-Jan Kiewiet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
