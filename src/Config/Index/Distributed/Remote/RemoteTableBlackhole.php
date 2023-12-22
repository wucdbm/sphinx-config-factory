<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index\Distributed\Remote;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;

enum RemoteTableBlackhole: int implements ConfigPart
{
    case true = 1;
    case false = 0;

    public function toString(): string
    {
        return sprintf('blackhole=%d', $this->value);
    }
}
