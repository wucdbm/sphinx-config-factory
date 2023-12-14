<?php

namespace Wucdbm\Sphinx\ConfigFactory\DTO;

enum SqlAttrType: string
{
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
