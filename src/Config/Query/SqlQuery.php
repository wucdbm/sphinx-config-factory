<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Query;

use Wucdbm\Sphinx\ConfigFactory\Config\OrderableConfigPart;
use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class SqlQuery implements OrderableConfigPart
{
    public function __construct(
        private SqlQueryType $type,
        private string $sql,
        private array $where = [],
        private ?SqlQueryRange $range = null,
    )
    {
    }

    public function getPriority(): int
    {
        return match($this->type) {
            SqlQueryType::pre => 300,
            SqlQueryType::sql => 301,
            SqlQueryType::post => 302,
            SqlQueryType::post_index => 303,
            default => 399,
        };
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
