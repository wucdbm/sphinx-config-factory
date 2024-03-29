<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\AttrMulti;

use Wucdbm\Sphinx\ConfigFactory\Config\OrderableConfigPart;
use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class SqlAttrMulti implements OrderableConfigPart
{
    public function __construct(
        private string $field,
        private SqlAttrMultiFieldType $type,
        private SqlAttrMultiSourceType $sourceType,
        private string $dataQuery,
        private ?string $rangeQuery = null,
    )
    {
    }

    public function getPriority(): int
    {
        return self::PRIORITY_ATTR_MULTI;
    }

    public function toString(): string
    {
        $attr = sprintf(
            'sql_attr_multi = %s %s from %s',
            $this->type->value,
            $this->field,
            $this->sourceType->value,
        );

        $lines = [$attr];

        if (!empty($this->dataQuery)) {
            $lines[] = ConfigHelper::indent(
                5,
                ConfigHelper::terminateLines(trim($this->dataQuery))
            );
        }

        if (!empty($this->rangeQuery)) {
            $lines[] = ConfigHelper::indent(
                5,
                ConfigHelper::terminateLines(trim($this->rangeQuery))
            );
        }

        return ConfigHelper::terminateLinesArray($lines, "; \\\n");
    }
}
