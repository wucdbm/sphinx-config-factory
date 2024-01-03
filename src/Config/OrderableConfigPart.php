<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config;

interface OrderableConfigPart extends ConfigPart
{
    public const PRIORITY_ATTR = 100;
    public const PRIORITY_ATTR_MULTI = 200;
    public const PRIORITY_COLUMNAR_CONFIG = 300;
    public const PRIORITY_QUERY = 400;

    public function getPriority(): int;
}
