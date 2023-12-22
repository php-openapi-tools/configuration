<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration\Package;

use EventSauce\ObjectHydrator\MapFrom;
use EventSauce\ObjectHydrator\PropertyCasters\CastListToType;
use OpenAPITools\Contract;

final readonly class State implements Contract\Package\State
{
    /** @param array<string> $additionalFiles */
    public function __construct(
        #[MapFrom('additionalFiles')]
        #[CastListToType('string')]
        public array|null $additionalFiles,
    ) {
    }
}
