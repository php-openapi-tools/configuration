<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration\Package;

final readonly class Metadata
{
    /** @param array<string> $tags */
    public function __construct(
        public string $name,
        public string $description,
        public array $tags,
    ) {
    }
}
