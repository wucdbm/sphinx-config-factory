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

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;
use Wucdbm\Sphinx\ConfigFactory\Config\ManticoreSource;
use Wucdbm\Sphinx\ConfigFactory\Config\Query\SqlQuery;
use Wucdbm\Sphinx\ConfigFactory\Config\Query\SqlQueryType;

class Factory
{
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

//    public function createIndexerConfig(array $config, $cleanup = false): string
//    {
//        return $this->createConfig('indexer', $config, self::INDEXER_CONFIGS, $cleanup);
//    }

//    public function createSearchdConfig(array $config, $cleanup = false): string
//    {
//        return $this->createConfig('searchd', $config, self::SEARCHD_CONFIGS, $cleanup);
//    }

//    public function createCommonConfig(array $config, $cleanup = false): string
//    {
//        return $this->createConfig('common', $config, self::COMMON_CONFIGS, $cleanup);
//    }

//    public function createConfig(string $type, array $config, array $keys, $cleanup = false): string
//    {
//        $configs = $this->cleanupConfig($config, $keys, $cleanup);
//
//        $lines = [];
//        foreach ($configs as $key => $value) {
//            if (is_array($value)) {
//                foreach ($value as $item) {
//                    $lines[] = ConfigHelper::indent(1, sprintf('%s = %s', $key, $item));
//                }
//            } else {
//                $lines[] = ConfigHelper::indent(1, sprintf('%s = %s', $key, $value));
//            }
//        }
//
//        $configString = implode("\n", $lines);
//
//        return <<<EOF
//{$type}
//{
//{$configString}
//}
//EOF;
//    }

//    public function cleanupConfig(array $config, array $keys, $cleanup = false): array
//    {
//        if (!$cleanup) {
//            return $config;
//        }
//
//        return array_intersect_key(
//            $config,
//            array_intersect_key(array_fill_keys($keys, null), $config),
//        );
//    }

    public function eof(): string
    {
        return "# --eof--\n";
    }

    public function listen(string $template): string
    {
        return str_replace(array_keys($this->hostVars), array_values($this->hostVars), $template);
    }

    public function configs(array $configs): string
    {
        return implode("\n\n", $configs);
    }

    public function configPartsToString(ConfigPart ...$parts): string
    {
        return implode("\n\n", array_map(fn(ConfigPart $part) => $part->toString(), $parts));
    }
}
