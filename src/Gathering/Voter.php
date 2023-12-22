<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration\Gathering;

use EventSauce\ObjectHydrator\MapFrom;
use OpenAPITools\Contract\Voter as VoterContract;

final readonly class Voter
{
    /**
     * @param array<class-string<VoterContract\ListOperation>>|null   $listOperation
     * @param array<class-string<VoterContract\StreamOperation>>|null $streamOperation
     */
    public function __construct(
        #[MapFrom('listOperation')]
        public array|null $listOperation,
        #[MapFrom('streamOperation')]
        public array|null $streamOperation,
    ) {
    }
}
