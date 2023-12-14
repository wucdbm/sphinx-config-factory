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

namespace Wucdbm\Sphinx\ConfigFactory\Config;

use Wucdbm\Sphinx\ConfigFactory\Config\Attr\SqlAttr;
use Wucdbm\Sphinx\ConfigFactory\Config\AttrMulti\SqlAttrMulti;
use Wucdbm\Sphinx\ConfigFactory\Config\Query\SqlQuery;
use Wucdbm\Sphinx\ConfigFactory\Config\Query\SqlQueryType;
use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class ManticoreSource implements ConfigPart
{
    /** @var SqlAttr[] */
    private array $attr;

    /** @var SqlAttrMulti[] */
    private array $attrMulti;

    /** @var SqlQuery[] */
    private array $queryPre;
    /** @var SqlQuery[] */
    private array $query;
    /** @var SqlQuery[] */
    private array $queryPost;
    /** @var SqlQuery[] */
    private array $queryPostIndex;

    /**
     * @param SqlAttr[] $attr
     * @param SqlAttrMulti[] $attrMulti
     * @param SqlQuery[] $queries
     */
    private function __construct(
        private string $name,
        private ?string $parent,
        array $attr,
        array $attrMulti,
        array $queries
    ) {
        $this->attr = $attr;
        $this->attrMulti = $attrMulti;

        $sql = $pre = $post = $postIndex = [];

        foreach ($queries as $query) {
            switch ($query->getType()) {
                case SqlQueryType::sql:
                    $sql[] = $query;
                    break;
                case SqlQueryType::pre:
                    $pre[] = $query;
                    break;
                case SqlQueryType::post:
                    $post[] = $query;
                    break;
                case SqlQueryType::post_index:
                    $postIndex[] = $query;
                    break;
                default:
                    throw new \RuntimeException(sprintf(
                        'Query Type "%s" Unknown',
                        $query->getType()->name
                    ));
            }
        }

        $this->queryPre = $pre;
        $this->query = $sql;
        $this->queryPost = $post;
        $this->queryPostIndex = $postIndex;
    }

    public static function create(string $name, ?string $parent): self
    {
        return new self($name, $parent, [], [], []);
    }

    public function withAttrs(array $attrs): self
    {
        return new self(
            $this->name,
            $this->parent,
            [
                ...$this->attr,
                ...SqlAttr::fromArray($attrs)
            ],
            $this->attrMulti,
            $this->getQueries(),
        );
    }

    public function withAttrMulti(SqlAttrMulti $attr): self
    {
        return new self(
            $this->name,
            $this->parent,
            $this->attr,
            [
                ...$this->attrMulti,
                $attr,
            ],
            $this->getQueries()
        );
    }

    public function withQuery(SqlQuery ...$queries): self
    {
        return new self(
            $this->name,
            $this->parent,
            $this->attr,
            $this->attrMulti,
            [
                ...$this->getQueries(),
                ...$queries
            ]
        );
    }

    private function getQueries(): array
    {
        return [
            ...$this->queryPre,
            ...$this->query,
            ...$this->queryPost,
            ...$this->queryPostIndex,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        $parentString = $this->parent ? sprintf(': %s', $this->parent) : '';

        $wat = [
            $this->attr,
            $this->attrMulti,
            $this->queryPre,
            $this->query,
            $this->queryPost,
            $this->queryPostIndex,
        ];

        $parts = array_reduce($wat, function(array $acc, array $item) {
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

        $content = ConfigHelper::indent(1, implode("\n", $content));

        return <<<EOF
source {$this->name} {$parentString}
{
{$content}
}
EOF;
    }
}
