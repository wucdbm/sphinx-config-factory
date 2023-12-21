<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Query;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;
use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class SqlQuery implements ConfigPart
{
    public function __construct(
        private SqlQueryType $type,
        private string $sql,
        private array $where = [],
        private ?SqlQueryRange $range = null,
    )
    {
    }

    public function getType(): SqlQueryType
    {
        return $this->type;
    }

    public function toString(): string
    {
        $sql = ConfigHelper::terminateLines(trim($this->sql));

        if (count($this->where)) {
            $whereString = ConfigHelper::terminateLines(implode("\n", $this->where));
            $sql = <<<ASD
$sql \
WHERE \
    $whereString
ASD;
        }

        $lines = [
            sprintf(
                '%s = %s',
                $this->type->toString(),
                $sql,
            )
        ];

        if ($this->range) {
            $lines = [
                $this->range->toString(),
                ...$lines
            ];
        }

        return implode("\n\n", $lines);
    }
}
