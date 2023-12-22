<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration;

use OpenAPITools\Configuration\Gathering\Schemas;
use OpenAPITools\Configuration\Gathering\Voter;

final readonly class Gathering
{
    public function __construct(
        public string $spec,
        public Voter|null $voter,
        public Schemas|null $schemas,
    ) {
    }
}
