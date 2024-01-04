<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;

/**
 * Removing documents in a plain table
 * A plain table can contain a directive called killlist_target that will tell the server it can provide a list of document IDs that should be removed from certain existing tables. The table can use either its document IDs as the source for this list or provide a separate list.
 *
 * killlist_target
 * Sets the table(s) that the kill-list will be applied to. Optional, default value is empty.
 *
 * When you use plain tables you often need to maintain not just a single table, but a set of them to be able to add/update/delete new documents sooner (read about delta table updates). n order to suppress matches in the previous (main) table that were updated or deleted in the next (delta) table, you need to:
 *
 * Create a kill-list in the delta table using sql_query_killlist
 * Specify main table as killlist_target in delta table settings:
 * ‹›
 * CONFIG
 * 📋
 * table products {
 * killlist_target = main:kl
 *
 * path = products
 * source = src_base
 * }
 * When killlist_target is specified, the kill-list is applied to all the tables listed in it on searchd startup. If any of the tables from killlist_target are rotated, the kill-list is reapplied to these tables. When the kill-list is applied, tables that were affected save these changes to disk.
 *
 * killlist_target has 3 modes of operation:
 *
 * killlist_target = main:kl . Document IDs from the kill-list of the delta table are suppressed in the main table (see sql_query_killlist ).
 * killlist_target = main:id . All document IDs from the delta table are suppressed in the main table. The kill-list is ignored.
 * killlist_target = main . Both document IDs from the delta table and its kill-list are suppressed in the main table.
 * Multiple targets can be specified, separated by commas like:
 *
 * killlist_target = table_one:kl,table_two:kl
 * You can change the killlist_target settings for a table without rebuilding it by using ALTER .
 *
 * However, since the 'old' main table has already written the changes to disk, the documents that were deleted in it will remain deleted even if it is no longer in the killlist_target of the delta table.
 */
class KillList implements ConfigPart
{
    private array $targets;
    public function __construct(
        KillListTarget ...$targets,
    )
    {
        $this->targets = $targets;
    }

    public function toString(): string
    {
        return sprintf(
            'killlist_target = %s',
            implode(
                ',',
                array_map(
                    fn(KillListTarget $target) => $target->toString(),
                    $this->targets,
                )
            )
        );
    }
}
