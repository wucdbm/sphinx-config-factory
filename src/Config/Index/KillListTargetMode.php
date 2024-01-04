<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index;

/**
 *
 */
enum KillListTargetMode: string
{
    /**
     * killlist_target = main:kl
     *
     * Document IDs from the kill-list of the delta table are suppressed in the main table (see sql_query_killlist ).
     */
    case kill_list = 'kill_list';

    /**
     * killlist_target = main:id
     *
     * All document IDs from the delta table are suppressed in the main table. The kill-list is ignored.
     */
    case id = 'id';

    /**
     * killlist_target = main
     * Both document IDs from the delta table and its kill-list are suppressed in the main table.
     */
    case all = 'all';
}
