<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration\Package;

use OpenAPITools\Contract;

final readonly class Templates implements Contract\Package\Templates
{
    /** @param array<string, mixed>|null $variables */
    public function __construct(
        public string $dir,
        public array|null $variables,
    ) {
    }
}
