<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Source;

use Wucdbm\Sphinx\ConfigFactory\Config\OrderableConfigPart;
use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class SourceLine implements OrderableConfigPart
{
    public function __construct(
        private string $line,
    )
    {
    }

    public function getPriority(): int
    {
        return self::PRIORITY_LINE;
    }

    public function toString(): string
    {
        return ConfigHelper::terminateLines($this->line);
    }
}
