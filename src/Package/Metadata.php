<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration\Package;

use OpenAPITools\Contract;

final readonly class Metadata implements Contract\Package\Metadata
{
    /** @param array<string> $keywords */
    public function __construct(
        public string $name,
        public string $description,
        public array $keywords,
    ) {
    }
}
