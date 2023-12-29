<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Manticore;

readonly class SearchdConfig extends AbstractCoreConfig
{
    public function __construct(array $config)
    {
        parent::__construct('searchd', $config);
    }

    /**
     * @return string[]
     */
    protected function getAllowedKeys(): array
    {
        return [
            'listen',
            'log',
            'query_log',
            'network_timeout',
            'read_timeout',
            'sphinxql_timeout',
            'client_timeout',
            'agent_query_timeout',
            'agent_connect_timeout',
            'pseudo_sharding',
            'max_children',
            'persistent_connections_limit',
            'pid_file',
            'seamless_rotate',
            'preopen_indexes',
            'preopen_tables',
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
            'max_threads_per_query',
            'threads',
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
    }
}
