<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;

readonly class KillListTarget implements ConfigPart
{
    public function __construct(
        private string $index,
        private KillListTargetMode $mode
    )
    {
    }

    public function toString(): string
    {
        return match ($this->mode) {
            KillListTargetMode::kill_list => sprintf('%s:kl', $this->index),
            KillListTargetMode::id => sprintf('%s:id', $this->index),
            KillListTargetMode::all => $this->index,
        };
    }
}
