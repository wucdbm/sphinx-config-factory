<?php

namespace Wucdbm\Sphinx\ConfigFactory;

enum SqlQueryPrePostType: string
{
    /**
     * Pre-fetch query or pre-query. This is a multi-value, optional setting, with the default being an empty list of queries. The pre-queries are executed before the sql_query in the order they appear in the configuration file. The results of the pre-queries are ignored.
     *
     * Pre-queries are useful in many ways. They can be used to set up encoding, mark records that are going to be indexed, update internal counters, set various per-connection SQL server options and variables, and so on.
     *
     * Perhaps the most frequent use of pre-query is to specify the encoding that the server will use for the rows it returns. Note that Manticore accepts only UTF-8 text. Two MySQL specific examples of setting the encoding are:
     *
     * sql_query_pre = SET CHARACTER_SET_RESULTS=utf8
     * sql_query_pre = SET NAMES utf8
     *
     * Also, specific to MySQL sources, it is useful to disable query cache (for indexer connection only) in pre-query, because indexing queries are not going to be re-run frequently anyway, and there's no sense in caching their results. That could be achieved with:
     *
     * sql_query_pre = SET SESSION query_cache_type=OFF
     */
    case sql_query_pre = 'sql_query_pre';

    /**
     * Post-fetch query. This is an optional setting, with the default value being empty.
     *
     * This query is executed immediately after sql_query completes successfully.
     * When the post-fetch query produces errors, they are reported as warnings, but indexing is not terminated.
     * Its result set is ignored.
     * Note that indexing is not yet completed at the point when this query gets executed, and further indexing may still fail.
     * Therefore, any permanent updates should not be done from here.
     * For instance, updates on a helper table that permanently change the last successfully indexed ID
     * should not be run from the sql_query_post query; they should be run from the sql_query_post_index query instead.
     */
    case sql_query_post = 'sql_query_post';

    /**
     * Post-processing query. This is an optional setting, with the default value being empty.
     *
     * This query is executed when indexing is fully and successfully completed. If this query produces errors, they are reported as warnings, but indexing is not terminated. Its result set is ignored. The $maxid macro can be used in its text; it will be expanded to the maximum document ID that was actually fetched from the database during indexing. If no documents were indexed, $maxid will be expanded to 0.
     *
     * Example:
     *
     * sql_query_post_index = REPLACE INTO counters ( id, val ) \
     * VALUES ( 'max_indexed_id', $maxid )
     *
     * The difference between sql_query_post and sql_query_post_index is that sql_query_post is run
     * immediately when Manticore receives all the documents, but further indexing may still fail for some other reason.
     * On the contrary, by the time the sql_query_post_index query gets executed, it is guaranteed that the table was created successfully.
     * Database connection is dropped and re-established because sorting phase can be very lengthy and would just time out otherwise.
     */
    case sql_query_post_index = 'sql_query_post_index';
}
