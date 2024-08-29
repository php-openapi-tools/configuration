<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration;

use EventSauce\ObjectHydrator\MapFrom;
use OpenAPITools\Configuration\Package\Destination;
use OpenAPITools\Configuration\Package\Metadata;
use OpenAPITools\Configuration\Package\QA;
use OpenAPITools\Configuration\Package\State;
use OpenAPITools\Configuration\Package\Templates;
use OpenAPITools\Contract;
use OpenAPITools\Utils\Namespace_;

final readonly class Package implements Contract\Package
{
    /** @param array<Contract\FileGenerator> $generators */
    public function __construct(
        public Metadata $metadata,
        public string $vendor,
        public string $name,
        public string|null $repository,
        public string|null $branch,
        #[MapFrom('targetVersion')]
        public string|null $targetVersion,
        public Templates|null $templates,
        public Destination $destination,
        public Namespace_ $namespace,
        public QA $qa,
        public State $state,
        #[MapFrom('generators')]
        public array $generators,
    ) {
    }
}
