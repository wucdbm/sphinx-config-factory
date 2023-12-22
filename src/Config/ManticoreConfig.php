<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config;

class ManticoreConfig implements ConfigPart
{
    /**
     * @var ConfigPart[]
     */
    private array $configs;

    public function __construct(
        ConfigPart ...$configs
    )
    {
        $this->configs = $configs;
    }

    public function toString(): string
    {
        implode("\n\n", array_map(fn(ConfigPart $part) => $part->toString(), $this->configs));
    }

}
