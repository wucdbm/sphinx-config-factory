<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config;

enum SqlAttrType: string
{
    case uint = 'uint';
    case bigint = 'bigint';
    case bool = 'bool';
    case float = 'float';
    case json = 'json';

    public function getAttr(): string
    {
        return sprintf(
            'sql_attr_%s',
            $this->value,
        );
    }
}
