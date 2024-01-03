<?php
/*
 * Copyright (C) 2016-2023 Taylor & Hart Limited
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains the property
 * of Taylor & Hart Limited and its suppliers, if any.
 *
 * All   intellectual   and  technical  concepts  contained  herein  are
 * proprietary  to  Taylor & Hart Limited  and  its suppliers and may be
 * covered  by  U.K.  and  foreign  patents, patents in process, and are
 * protected in full by copyright law. Dissemination of this information
 * or  reproduction  of this material is strictly forbidden unless prior
 * written permission is obtained from Taylor & Hart Limited.
 *
 * ANY  REPRODUCTION, MODIFICATION, DISTRIBUTION, PUBLIC PERFORMANCE, OR
 * PUBLIC  DISPLAY  OF  OR  THROUGH  USE OF THIS SOURCE CODE WITHOUT THE
 * EXPRESS  WRITTEN CONSENT OF RARE PINK LIMITED IS STRICTLY PROHIBITED,
 * AND  IN  VIOLATION  OF  APPLICABLE LAWS. THE RECEIPT OR POSSESSION OF
 * THIS  SOURCE CODE AND/OR RELATED INFORMATION DOES NOT CONVEY OR IMPLY
 * ANY  RIGHTS  TO REPRODUCE, DISCLOSE OR DISTRIBUTE ITS CONTENTS, OR TO
 * MANUFACTURE,  USE, OR SELL ANYTHING THAT IT MAY DESCRIBE, IN WHOLE OR
 * IN PART.
 */

namespace Wucdbm\Sphinx\ConfigFactory\Config\Source;

use Wucdbm\Sphinx\ConfigFactory\Config\Attr\SqlAttr;
use Wucdbm\Sphinx\ConfigFactory\Config\AttrMulti\SqlAttrMulti;
use Wucdbm\Sphinx\ConfigFactory\Config\BlankLine;
use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;
use Wucdbm\Sphinx\ConfigFactory\Config\OrderableConfigPart;
use Wucdbm\Sphinx\ConfigFactory\Config\Query\SqlQuery;
use Wucdbm\Sphinx\ConfigFactory\Config\Query\SqlQueryType;
use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;
use Wucdbm\Sphinx\ConfigFactory\Config\DatabaseConnection;

readonly class Source implements ConfigPart
{
    /** @var OrderableConfigPart[] */
    private array $configs;

    private function __construct(
        private string $name,
        private string|DatabaseConnection $parent,
        OrderableConfigPart ...$configs
    ) {
        $this->configs = $configs;
    }

    private function clone(OrderableConfigPart ...$configs): self
    {
        return new self(
            $this->name,
            $this->parent,
            ...[
                ...$this->configs,
                ...$configs
            ],
        );
    }

    public static function create(string $name, string|DatabaseConnection $parent): self
    {
        return new self($name, $parent);
    }

    public function withAttrs(array $attrs): self
    {
        return $this->clone(
            ...SqlAttr::fromArray($attrs),
        );
    }

    public function withAttrMulti(SqlAttrMulti ...$attr): self
    {
        return $this->clone(
            ...$attr,
        );
    }

    public function withQuery(SqlQuery ...$queries): self
    {
        return $this->clone(
            ...$queries,
        );
    }

    public function withPreQuery(string $query): self
    {
        return $this->clone(
            new SqlQuery(SqlQueryType::pre, $query),
        );
    }

    public function withPostQuery(string $query): self
    {
        return $this->clone(
            new SqlQuery(SqlQueryType::post, $query),
        );
    }

    public function withPostIndexQuery(string $query): self
    {
        return $this->clone(
            new SqlQuery(SqlQueryType::post_index, $query),
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        $parentString = $connectionString = '';

        if (is_string($this->parent)) {
            $parentString = sprintf(' : %s', $this->parent);
        } else {
            $connectionString = $this->parent->toString();
        }

        $groupedConfigs = [];

        foreach ($this->configs as $config) {
            if (!isset($groupedConfigs[$config->getPriority()])) {
                $groupedConfigs[$config->getPriority()] = [];
            }

            $groupedConfigs[$config->getPriority()] = $config;
        }

        ksort($groupedConfigs);

//        $configGroups = [
//            $this->attr,
//            $this->attrMulti,
//            $this->queryPre,
//            $this->query,
//            $this->queryPost,
//            $this->queryPostIndex,
//        ];

        $parts = array_reduce($groupedConfigs, function(array $acc, array $item) {
            if (!count($acc)) {
                return $item;
            }

            if (!count($item)) {
                return $acc;
            }

            return [
                ...$acc,
                new BlankLine(),
                ...$item,
            ];
        }, []);

        $content = array_map(
            fn (ConfigPart $part) => $part->toString(),
            $parts
        );

        if ($connectionString) {
            $content = [
                $connectionString,
                ...$content
            ];
        }

        $content = ConfigHelper::indent(1, implode("\n", $content));

        return <<<EOF
source {$this->name}{$parentString}
{
{$content}
}
EOF;
    }
}
