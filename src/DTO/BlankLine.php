<?php

namespace Wucdbm\Sphinx\ConfigFactory\DTO;

readonly class BlankLine implements ConfigPart
{
    public function toString(): string
    {
        return '';
    }
}
