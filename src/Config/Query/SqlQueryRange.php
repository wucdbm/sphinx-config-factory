<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Query;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;
use Wucdbm\Sphinx\ConfigFactory\Config\Query\SqlQueryType;
use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

/**
 * Ranged queries
 * Main query, which needs to fetch all the documents, can impose a read lock on the whole table and stall the concurrent queries (e.g. INSERTs to MyISAM table), waste a lot of memory for result set, etc. To avoid this, Manticore supports so-called ranged queries. With ranged queries, Manticore first fetches min and max document IDs from the table, and then substitutes different ID intervals into main query text and runs the modified query to fetch another chunk of documents. Here's an example.
 *
 * Ranged query usage example:
 *
 * sql_query_range = SELECT MIN(id), MAX(id) FROM documents
 * sql_range_step = 1000
 * sql_query = SELECT * FROM documents WHERE id >= $start AND id <= $end
 */
readonly class SqlQueryRange implements ConfigPart
{
    public function __construct(
        /**
         * sql_query_range
         * Defines the range query. The query specified in this option must fetch min and max document IDs that will be used as range boundaries. It must return exactly two integer fields, min ID first and max ID second; the field names are ignored. When enabled, sql_query will be required to contain $start and $end macros. Note that the intervals specified by $start..$end will not overlap, so you should not remove document IDs that are exactly equal to $start or $end from your query.
         */
        private string $query,
        /**
         * sql_range_step
         * This directive defines the range query step. The default value is 1024.
         */
        private int $step,
        /**
         * sql_ranged_throttle
         * This directive can be used to throttle the ranged query. By default, there is no throttling. Values for sql_ranged_throttle should be specified in milliseconds.
         *
         * Throttling can be useful when the indexer imposes too much load on the database server. It causes the indexer to sleep for a given amount of time once per each ranged query step. This sleep is unconditional and is performed before the fetch query.
         */
        private ?int $throttle,
    )
    {
    }

    public function toString(): string
    {
        $lines = [
            sprintf(
                'sql_query_range = %s',
                $this->query,
            ),
            sprintf(
                'sql_range_step = %d',
                $this->step,
            ),
        ];

        if ($this->throttle) {
            $lines[] = sprintf(
                'sql_ranged_throttle = %d',
                $this->throttle
            );
        }

        return implode("\n", $lines);
    }
}
