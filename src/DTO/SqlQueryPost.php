<?php

namespace Wucdbm\Sphinx\ConfigFactory\DTO;

use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class SqlQueryPost implements ConfigPart
{
    public function __construct(
        private string $query
    )
    {
    }

    public function toString(): string
    {
        return sprintf(
            'sql_query_post = %s',
            ConfigHelper::terminateLines($this->query)
        );
    }
}
