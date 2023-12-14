<?php

namespace Wucdbm\Sphinx\ConfigFactory\DTO;

use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class SqlQuery implements ConfigPart
{
    public function __construct(
        private string $sql,
        private array $where = []
    )
    {
    }

    public function toString(): string
    {
        $sql = ConfigHelper::terminateLines($this->sql);

        if (count($this->where)) {
            $whereString = ConfigHelper::terminateLines(implode("\n", $this->where));
            $sql = <<<ASD
{$sql} \
WHERE \
    {$whereString}
ASD;
        }

        return <<<EOF
sql_query = \
    {$sql}
EOF;
    }
}
