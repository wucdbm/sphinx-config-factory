<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config;

enum SqlAttrMultiFieldType: string
{
    case uint = 'uint';
    case bigint = 'bigint';
    case timestamp = 'timestamp';
}
