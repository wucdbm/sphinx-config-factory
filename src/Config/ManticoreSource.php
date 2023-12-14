<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config;

use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class ManticoreSource implements ConfigPart
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

    public function getName(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        $parentString = $this->parent ? sprintf(': %s', $this->parent) : '';

        $content = array_map(
            fn(ConfigPart $part) => $part->toString(),
            $this->configParts
        );

        $content = ConfigHelper::indent(1, implode("\n", $content));

        return <<<EOF
source {$this->name} {$parentString}
{
{$content}
}
EOF;
    }
}
