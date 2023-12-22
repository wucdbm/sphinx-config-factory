<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index;

use Wucdbm\Sphinx\ConfigFactory\Config\Index\ManticoreIndex;

readonly class TargetIndex
{
    public function __construct(
        public string $ip,
        public string $port,
        public ManticoreIndex $index,
    )
    {
    }
}
