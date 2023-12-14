<?php

namespace Wucdbm\Sphinx\ConfigFactory\DTO;

use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class SqlQueryPostIndex implements ConfigPart
{
    public function __construct(
        private string $query
    )
    {
    }

    public function toString(): string
    {
        return sprintf(
            'sql_query_post_index = %s',
            ConfigHelper::terminateLines($this->query)
        );
    }
}
