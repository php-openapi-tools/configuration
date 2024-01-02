<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration;

final readonly class State
{
    public function __construct(
        public string $file,
    ) {
    }
}
