<?php

namespace Wucdbm\Sphinx\ConfigFactory\DTO;

enum SqlAttrMultiSourceType: string
{
    case field = 'field';
    case query = 'query';
    case ranged_query = 'ranged-query';
    case ranged_main_query = 'ranged-main-query';
}
