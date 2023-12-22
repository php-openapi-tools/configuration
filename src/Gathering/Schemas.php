<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration\Gathering;

use EventSauce\ObjectHydrator\MapFrom;

final readonly class Schemas
{
    public function __construct(
        #[MapFrom('allowDuplication')]
        public bool $allowDuplication,
        #[MapFrom('useAliasesForDuplication')]
        public bool $useAliasesForDuplication,
    ) {
    }
}
