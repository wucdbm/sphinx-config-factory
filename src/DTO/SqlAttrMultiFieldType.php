<?php

namespace Wucdbm\Sphinx\ConfigFactory\DTO;

enum SqlAttrMultiFieldType: string
{
    case uint = 'uint';
    case bigint = 'bigint';
    case timestamp = 'timestamp';
}
