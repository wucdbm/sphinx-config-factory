# Example Usage

- Create a sphinx.conf.php file
- Create your index queries
- `bin/indexer --all /path/to/sphinx.conf.php`

```php
#!/usr/bin/php
#
# Sphinx configuration file sample
#
# WARNING! While this sample file mentions all available options,
# it contains (very) short helper descriptions only. Please refer to
# doc/sphinx.html for details.
#
<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Wucdbm\Sphinx\ConfigFactory\EnvContainer;
use Wucdbm\Sphinx\ConfigFactory\Factory;

require dirname(__DIR__) . '/../vendor/autoload.php';

umask(0000);

Debug::enable();

$dotEnv = new Dotenv();
$dotEnv->bootEnv(__DIR__ . '/path/to/main/env/file/.env');
$dotEnv->load(__DIR__ . '/path/to/sphinx/env/file/.env.local');

$env = new EnvContainer($_ENV);

// Get ENV variables from your app
// This allows you to have seamless deployments
// While using your local environment/configurations on local setups
$dsn = $env->get('DATABASE_URL');
$port = $env->get('SPHINX_PORT');
$mysqlPort = $env->get('SPHINX_MYSQL_PORT');
$ip = $env->get('SPHINX_HOST');

// Ensure those local env vars are present
// This allows you to store sphinx data somewhere else on your local dev machine
$env->ensure([
    'INDEX_STORAGE',
    'LOG_PATH',
    'QUERY_LOG_PATH',
    'PID_PATH',
]);

$indexStorage = $env->get('INDEX_STORAGE');
$logPath = $env->get('LOG_PATH');
$queryLogPath = $env->get('QUERY_LOG_PATH');
$pidPath = $env->get('PID_PATH');

$db = parse_url($dsn);

$factory = new Factory([
    'sql_query_pre' => [
        'SET NAMES utf8',
        'SET CHARACTER SET utf8',
    ],
    // Host vars are used in Factory::listen() templates (ES6 interpolation-like syntax)
    'host_vars' => [
        'ip' => $ip,
        'port' => $port,
        'mysql_port' => $mysqlPort,
    ],
]);

$dbConfig = [
    'type' => $db['scheme'],
    'host' => $db['host'],
    'port' => $db['port'],
    'name' => ltrim($db['path'], '/'),
    'user' => $db['user'],
    'pass' => $db['pass'],
];

$baseIndexName = 'base_source';

echo $factory->configs([
    // Create source to inherit from
    $factory->createBaseSource($baseIndexName, $dbConfig),
    // Create source, extend $baseIndexName
    $factory->createSource('main_index', $baseIndexName, [
        # If you have extra columns in sphinx_index_meta, INSERT INTO is desirable over REPLACE INTO as that'll replace the whole record
        $factory->sqlQueryPre([
            sprintf(
                '
                    INSERT INTO sphinx_index_meta (index_name, max_id, last_update) 
                            SELECT \'%s\', IFNULL(MAX(id), 0), UNIX_TIMESTAMP() FROM data_table
                        ON DUPLICATE KEY UPDATE max_id = (SELECT IFNULL(MAX(id), 0) FROM data_table), last_update = UNIX_TIMESTAMP()
                ',
                'main_index'
            ),
        ]),
        $factory->createAttrs([
            'field_id' => 'uint',

            'boolean_attr' => 'bool',

            'some_json' => 'json',

            'another_field' => 'bigint',
        ]),
        $factory->sqlAttrMultiRangedQuery(
            'uint',
            'mva_attr',
            'SELECT some_id AS id, mva_attr_id AS mva_attr FROM another_table at WHERE id >= $start AND id <= $end',
            'SELECT MIN(id), MAX(id) FROM another_table'
        ),
        $factory->createSourceSql(
            'SELECT id, some_attr FROM table',
        ),
    ]),
    // Optionally create delta source, extend the main one
    $factory->createSource('delta_index', 'main_index', [
        $factory->sqlQueryPre([
            sprintf(
                'REPLACE INTO sphinx_index_meta (index_name, max_id, last_update) SELECT \'%s\', IFNULL(MAX(id), 0), UNIX_TIMESTAMP() FROM data_table',
                'delta_index'
            ),
        ]),
        // possibly create range MVAs that fit the delta index with proper WHERE clauses
        $factory->createSourceSql(
            'SELECT id, some_attr FROM table',
            [
                'another_field = \'some_val\''
            ]
        ),
    ]),
    // Create index for main
    $factory->createIndex('main_index', 'main_index', $indexStorage),
    // Create delta index
    $factory->createIndex('delta_index', 'delta_index', $indexStorage),
    $factory->createDistributedIndex(
        'distributed_index',
        [
            [
                'ip' => $ip,
                'port' => $port,
                'name' => 'main_index',
            ],
            [
                'ip' => $ip,
                'port' => $port,
                'name' => 'delta_index',
            ],
        ]
    ),
    $factory->createIndexerConfig([
        'mem_limit' => '2047M',
    ]),
    $factory->createSearchdConfig([
        'listen' => [
            $factory->listen('${ip}:${port}:sphinx'),
            $factory->listen('${ip}:${mysql_port}:mysql41'),
        ],
        'log' => $logPath,
        'query_log' => $queryLogPath,
        'read_timeout' => '5',
        'client_timeout' => '300',
        'max_children' => '150',
        'persistent_connections_limit' => '30',
        'pid_file' => $pidPath,
        'seamless_rotate' => '1',
        'preopen_indexes' => '1',
        'unlink_old' => '1',
        'max_packet_size' => '32M',
        'max_filters' => '256',
        'max_filter_values' => '8192',
        'max_batch_queries' => '32',
        'workers' => 'threads',
        'dist_threads' => '4',
        // disable binlog
        'binlog_path' => '# disable logging',
    ]),
    $factory->createCommonConfig([]),
    $factory->eof()
]);
```