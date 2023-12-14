<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config;

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
