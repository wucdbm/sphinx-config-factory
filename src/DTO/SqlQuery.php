<?php

namespace Wucdbm\Sphinx\ConfigFactory\DTO;

use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class SqlQuery implements ConfigPart
{
    public function __construct(
        private SqlQueryType $type,
        private string $sql,
        private array $where = []
    )
    {
    }

    public function toString(): string
    {
        $sql = ConfigHelper::terminateLines(trim($this->sql));

        if (count($this->where)) {
            $whereString = ConfigHelper::terminateLines(implode("\n", $this->where));
            $sql = <<<ASD
{$sql} \
WHERE \
    {$whereString}
ASD;
        }

        $type = $this->type->toString();
        return <<<EOF
{$type} = {$sql}
EOF;
    }
}
