<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config;

interface OrderableConfigPart extends ConfigPart
{
    public function getPriority(): int;
}
