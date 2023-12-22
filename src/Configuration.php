<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration;

use EventSauce\ObjectHydrator\MapFrom;

final readonly class Configuration
{
    /** @param array<Package|ListOfPackages> $packages */
    public function __construct(
        public State $state,
        public Gathering $gathering,
        #[MapFrom('packages')]
        public array $packages,
    ) {
    }
}
