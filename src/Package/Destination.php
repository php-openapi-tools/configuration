<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration\Package;

use OpenAPITools\Contract;

final readonly class Destination implements Contract\Package\Destination
{
    public function __construct(
        public string $root,
        public string $source,
        public string $test,
    ) {
    }
}
