<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config;

use Wucdbm\Sphinx\ConfigFactory\DatabaseConnection;

readonly class BaseSource implements ConfigPart
{
    public function __construct(
        private string $name,
        private DatabaseConnection $connection
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toString(): string {
        return <<<EOF
source {$this->name}
{
    type                    = {$this->connection->type}

    sql_host                = {$this->connection->host}
    sql_port                = {$this->connection->port}  # optional, default is 3306
    sql_db                  = {$this->connection->database}
    sql_user                = {$this->connection->username}
    sql_pass                = {$this->connection->password}
}
EOF;
    }
}
