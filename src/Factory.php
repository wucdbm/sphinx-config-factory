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

use Wucdbm\Sphinx\ConfigFactory\DTO\ConfigPart;
use Wucdbm\Sphinx\ConfigFactory\DTO\SqlAttr;

class Factory {

    public const ATTR_MULTI_TYPE_UINT = 'uint';
    public const ATTR_MULTI_TYPE_BIGINT = 'bigint';
    public const ATTR_MULTI_TYPE_TIMESTAMP = 'timestamp';

    public const INDEXER_CONFIGS = [
        'mem_limit',
        'max_iops',
        'max_iosize',
        'max_xmlpipe2_field',
        'write_buffer',
        'max_file_field_buffer',
        'on_file_field_error',
        'lemmatizer_cache',
    ];

    public const SEARCHD_CONFIGS = [
        'listen',
        'log',
        'query_log',
        'read_timeout',
        'client_timeout',
        'max_children',
        'persistent_connections_limit',
        'pid_file',
        'seamless_rotate',
        'preopen_indexes',
        'unlink_old',
        'attr_flush_period',
        'mva_updates_pool',
        'max_packet_size',
        'max_filters',
        'max_filter_values',
        'listen_backlog',
        'read_buffer',
        'read_unhinted',
        'max_batch_queries',
        'subtree_docs_cache',
        'subtree_hits_cache',
        'workers',
        'dist_threads',
        'binlog_path',
        'binlog_flush',
        'binlog_max_log_size',
        'thread_stack',
        'expansion_limit',
        'rt_flush_period',
        'query_log_format',
        'mysql_version_string',
        'collation_server',
        'collation_libc_locale',
        'watchdog',
        'predicted_time_costs',
        'sphinxql_state',
        'rt_merge_iops',
        'rt_merge_maxiosize',
        'ha_ping_interval',
        'ha_period_karma',
        'prefork_rotation_throttle',
        'snippets_file_prefix',
    ];

    public const COMMON_CONFIGS = [
        'lemmatizer_base',
        'on_json_attr_error',
        'json_autoconv_numbers',
        'json_autoconv_keynames',
        'plugin_dir',
    ];

    private array $queryPre;
    private array $queryPost;
    private array $queryPostIndex;
    private array $hostVars;

    public function __construct(array $options) {
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

    public function sqlQueryPre(array $extra = []): string {
        return $this->sqlQueryPrePostIndex(
            SqlQueryPrePostType::sql_query_pre,
            [
                ...$this->queryPre,
                ...$extra,
            ]
        );
    }

    public function sqlQueryPost(array $extra = []): string {
        return $this->sqlQueryPrePostIndex(
            SqlQueryPrePostType::sql_query_post,
            [
                ...$this->queryPost,
                ...$extra,
            ]
        );
    }

    public function sqlQueryPostIndex(array $extra = []): string {
        return $this->sqlQueryPrePostIndex(
            SqlQueryPrePostType::sql_query_post_index,
            [
                ...$this->queryPostIndex,
                ...$extra,
            ]
        );
    }

    private function sqlQueryPrePostIndex(SqlQueryPrePostType $type, array $queries = []): string {
        return implode("\n", array_map(function (string $query) use ($type) {
            if (false !== strpos($query, "\n")) {
                $lines = explode("\n", $query);
                $lines = array_map(trim(...), $lines);
                $lines = array_filter($lines, static function(string $line) {
                    return (bool)$line;
                });
                $query = implode(" ", $lines);
            }

            return $this->indent(1, sprintf('%s = %s', $type->value, $query));
        }, $queries));
    }

    public function configPartsToStringArray(ConfigPart ...$parts): array
    {
        return array_map(fn(ConfigPart $part) => $part->toString(), $parts);
    }

    public function indent(int $times, string $string): string {
        $spaces = $times * 4;
        $lines = explode("\n", $string);

        return implode(
            "\n",
            array_map(
                static function ($line) use ($spaces) {
                    return str_repeat(' ', $spaces).$line;
                },
                $lines
            )
        );
    }

    public function sqlAttrMultiRangedQuery(
        string $type,
        string $name,
        string $dataQuery,
        string $rangeQuery
    ): string {
        $lines = [
            $this->indent(1, sprintf(
                'sql_attr_multi = %s %s from ranged-query',
                $type,
                $name
            )),
            $this->indent(6, $dataQuery),
            $this->indent(6, $rangeQuery),
        ];

        return implode("; \\\n", $lines);
    }

    public function createBaseSource(string $name, DatabaseConnection $connection): string {
        return <<<EOF
source {$name}
{
    type                    = {$connection->type}

    sql_host                = {$connection->host}
    sql_port                = {$connection->port}  # optional, default is 3306
    sql_db                  = {$connection->database}
    sql_user                = {$connection->username}
    sql_pass                = {$connection->password}
}
EOF;
    }

    public function createSource(string $name, ?string $parent, array $lines): string {
        $parentString = $parent ? sprintf(': %s', $parent) : '';

        $content = implode("\n\n", $lines);

        return <<<EOF
source {$name} {$parentString}
{
{$content}
}
EOF;
    }

    public function terminateLines(string $lines): string {
        return substr(
            implode(
                "\n",
                array_map(
                    static function (string $line) {
                        return $line.' \\';
                    },
                    explode("\n", $lines)
                )
            ),
            0,
            -2
        );
    }

    public function createAttrs(array $attrs): string {
        $lines = [];

        foreach ($attrs as $name => $type) {
            $lines[] = $this->indent(1, sprintf(
                'sql_attr_%s = %s',
                $type,
                $name
            ));
        }

        return implode("\n", $lines);
    }

    /**
     * @return SqlAttr[]
     */
    public function createSqlAttrs(array $attrs): array {
        $lines = [];

        foreach ($attrs as $name => $type) {
            $lines[] = new SqlAttr()
            $lines[] = $this->indent(1, sprintf(
                'sql_attr_%s = %s',
                $type,
                $name
            ));
        }

        return implode("\n", $lines);
    }

    public function createFields(array $attrs): string {
        $lines = [];

        foreach ($attrs as $name => $type) {
            $lines[] = $this->indent(1, sprintf(
                'sql_field_%s = %s',
                $type,
                $name
            ));
        }

        return implode("\n", $lines);
    }

    public function createSourceSql(
        string $sql,
        array $where = []
    ): string {
        if (count($where)) {
            $whereString = $this->terminateLines(implode("\n", $where));
            $sql = <<<ASD
{$sql} \
WHERE \
    {$whereString}
ASD;
        }

        $sql_query = <<<EOF
sql_query = \
    {$sql}
EOF;

        return $this->indent(1, $sql_query);
    }

    public function createIndex(
        string $name,
        string $source,
        string $storage
    ): string {
        return <<<EOF
index {$name}
{
    source                  = {$source}
    path                    = {$storage}/{$name}
    min_word_len            = 2
    min_prefix_len          = 2, max_substring_len = 6
}
EOF;
    }

    public function createDistributedIndex(
        string $name,
        array $indexes
    ): string {
        $str = $this->indent(
            1,
            implode(
                "\n",
                array_map(static function (array $index) {
                    return sprintf(
                        'agent = %s:%s:%s',
                        $index['ip'],
                        $index['port'],
                        $index['name'],
                    );
                }, $indexes)
            )
        );

        return <<<EOF
index {$name}
{
    type = distributed
{$str}
}
EOF;
    }

    public function cleanupConfig(array $config, array $keys, $cleanup = false): array {
        if (!$cleanup) {
            return $config;
        }

        return array_intersect_key(
            $config,
            array_intersect_key(array_fill_keys($keys, null), $config),
        );
    }

    public function createConfig(string $type, array $config, array $keys, $cleanup = false): string {
        $configs = $this->cleanupConfig($config, $keys, $cleanup);

        $lines = [];
        foreach ($configs as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $lines[] = $this->indent(1, sprintf('%s = %s', $key, $item));
                }
            } else {
                $lines[] = $this->indent(1, sprintf('%s = %s', $key, $value));
            }
        }

        $configString = implode("\n", $lines);

        return <<<EOF
{$type}
{
{$configString}
}
EOF;
    }

    public function createIndexerConfig(array $config, $cleanup = false): string {
        return $this->createConfig('indexer', $config, self::INDEXER_CONFIGS, $cleanup);
    }

    public function createSearchdConfig(array $config, $cleanup = false): string {
        return $this->createConfig('searchd', $config, self::SEARCHD_CONFIGS, $cleanup);
    }

    public function createCommonConfig(array $config, $cleanup = false): string {
        return $this->createConfig('common', $config, self::COMMON_CONFIGS, $cleanup);
    }

    public function eof(): string {
        return "# --eof--\n";
    }

    public function listen(string $template): string {
        return str_replace(array_keys($this->hostVars), array_values($this->hostVars), $template);
    }

    public function configs(array $configs): string {
        return implode("\n\n", $configs);
    }
}
