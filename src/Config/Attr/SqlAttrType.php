<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Attr;

enum SqlAttrType: string
{
    case string = 'string';
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
