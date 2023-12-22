<?php

/*
 * This file is part of the wucdbm/sphinx-config-factory package.
 *
 * Copyright (c) Martin Kirilov <wucdbm@gmail.com>.
 *
 * Author Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wucdbm\Sphinx\ConfigFactory;

use Wucdbm\Sphinx\ConfigFactory\Config\Source\ManticoreSource;
use Wucdbm\Sphinx\ConfigFactory\Config\Query\SqlQuery;
use Wucdbm\Sphinx\ConfigFactory\Config\Query\SqlQueryType;

class Factory
{
    private array $queryPre;
    private array $queryPost;
    private array $queryPostIndex;
    private array $hostVars;

    public function __construct(array $options)
    {
        $this->queryPre = $options['sql_query_pre'] ?? [];
        $this->queryPost = $options['sql_query_post'] ?? [];
        $this->queryPostIndex = $options['sql_query_post_index'] ?? [];
        $hostVars = $options['host_vars'] ?? [];
        $this->hostVars = array_combine(
            array_map(static function (string $var) {
                return sprintf('${%s}', $var);
            }, array_keys($hostVars)),
            array_values($hostVars)
        );
    }

    public function sqlQueryPre(array $extra = []): array
    {
        return $this->createSqlQueries(
            SqlQueryType::pre,
            [
                ...$this->queryPre,
                ...$extra,
            ]
        );
    }

    public function sqlQueryPost(array $extra = []): array
    {
        return $this->createSqlQueries(
            SqlQueryType::post,
            [
                ...$this->queryPost,
                ...$extra,
            ]
        );
    }

    public function sqlQueryPostIndex(array $extra = []): array
    {
        return $this->createSqlQueries(
            SqlQueryType::post_index,
            [
                ...$this->queryPostIndex,
                ...$extra,
            ]
        );
    }

    private function createSqlQueries(SqlQueryType $type, array $queries = []): array
    {
        return array_map(
            fn(string $query) => new SqlQuery($type, $query),
            $queries
        );
    }

    public function getQueryPre(): array
    {
        return $this->queryPre;
    }

    public function getQueryPost(): array
    {
        return $this->queryPost;
    }

    public function getQueryPostIndex(): array
    {
        return $this->queryPostIndex;
    }

    public function createSource(string $name, ?string $parent): ManticoreSource
    {
        return ManticoreSource::create($name, $parent)
            ->withQuery(...$this->sqlQueryPre())
            ->withQuery(...$this->sqlQueryPost())
            ->withQuery(...$this->sqlQueryPostIndex());
    }

    public function listen(string $template): string
    {
        return str_replace(array_keys($this->hostVars), array_values($this->hostVars), $template);
    }
}
