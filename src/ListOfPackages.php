<?php

declare(strict_types=1);

namespace OpenAPITools\Configuration;

use EventSauce\ObjectHydrator\MapFrom;
use OpenAPITools\Configuration\Package\QA;
use OpenAPITools\Configuration\Package\State;
use OpenAPITools\Configuration\Package\Templates;
use OpenAPITools\Contract;
use OpenAPITools\Representation\Representation;
use OpenAPITools\Utils\Namespace_;

final readonly class ListOfPackages
{
    /** @param array<Contract\FileGenerator> $generators */
    public function __construct(
        private string $vendor,
        private string $name,
        private string|null $repository,
        private string|null $branch,
        #[MapFrom('targetVersion')]
        private string|null $targetVersion,
        private Templates|null $templates,
        private Namespace_ $namespace,
        private QA $qa,
        private State $state,
        #[MapFrom('generators')]
        private array $generators,
    ) {
    }

    /** @return iterable<Package> */
    public function list(Configuration $configuration, Representation $representation): iterable
    {
        yield from [];
    }
}
