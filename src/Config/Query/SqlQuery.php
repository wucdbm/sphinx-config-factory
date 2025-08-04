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
            SqlQueryType::pre => self::PRIORITY_QUERY,
            SqlQueryType::sql => self::PRIORITY_QUERY + 1,
            SqlQueryType::post => self::PRIORITY_QUERY + 2,
            SqlQueryType::post_index => self::PRIORITY_QUERY + 3,
            SqlQueryType::pre_all => self::PRIORITY_QUERY + 4,
            SqlQueryType::kill_list => self::PRIORITY_QUERY + 5,
            default => self::PRIORITY_QUERY + 99,
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
            $whereString = ConfigHelper::indent(
                1,
                ConfigHelper::terminateLines(
                    implode("\nAND\n", $this->where)
                )
            );
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
