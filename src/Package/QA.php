<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration\Package;

use OpenAPITools\Configuration\Package\QA\Tool;
use OpenAPITools\Contract;

final readonly class QA implements Contract\Package\QA
{
    public function __construct(
        public Tool|null $phpcs,
        public Tool|null $phpstan,
        public Tool|null $psalm,
    ) {
    }
}
