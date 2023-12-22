<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Manticore;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;
use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

abstract readonly class AbstractCoreConfig implements ConfigPart
{

    public function __construct(
        protected string $type,
        protected array $config,
    )
    {
    }

    /**
     * @return string[]
     */
    abstract protected function getAllowedKeys(): array;

    public function toString(): string
    {
        $config = ConfigHelper::cleanupConfig($this->config, $this->getAllowedKeys());

        $lines = [];
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $lines[] = ConfigHelper::indent(1, sprintf('%s = %s', $key, $item));
                }
            } else {
                $lines[] = ConfigHelper::indent(1, sprintf('%s = %s', $key, $value));
            }
        }

        $configString = implode("\n", $lines);

        return <<<EOF
{$this->type}
{
{$configString}
}
EOF;
    }
}
