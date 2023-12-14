<?php

namespace Wucdbm\Sphinx\ConfigFactory\DTO;

readonly class Source implements ConfigPart
{
    /** @var ConfigPart[] */
    private array $configParts;

    public function __construct(
        private string $name,
        private ?string $parent,
        ConfigPart ...$configParts
    )
    {
        $this->configParts = $configParts;
    }

    public function toString(): string
    {

    }
}
