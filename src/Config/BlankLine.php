<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config;

readonly class BlankLine implements ConfigPart
{
    public function toString(): string
    {
        return '';
    }
}
