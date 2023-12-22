<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration\Package\QA;

use EventSauce\ObjectHydrator\MapFrom;
use OpenAPITools\Contract;

final readonly class Tool implements Contract\Package\QA\Tool
{
    public function __construct(
        public bool $enabled,
        #[MapFrom('configFilePath')]
        public string|null $configFilePath,
    ) {
    }
}
