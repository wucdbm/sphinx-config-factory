<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;

readonly class ColumnarConfig implements ConfigPart
{
    public function __construct(
        /**
         * columnar_attrs
         * columnar_attrs = *
         * columnar_attrs = id, attr1, attr2, attr3
         *
         * This configuration setting determines which attributes should be stored in the columnar storage instead of the row-wise storage.
         *
         * You can set columnar_attrs = * to store all supported data types in the columnar storage.
         *
         * Additionally, id is a supported attribute to store in the columnar storage.
         *
         * @var string[]
         */
        private array $fields = [],

        /**
         * columnar_strings_no_hash
         * columnar_strings_no_hash = attr1, attr2, attr3
         *
         * By default, all string attributes stored in columnar storage store pre-calculated hashes.
         * These hashes are used for grouping and filtering.
         * However, they occupy extra space, and if you don't need to group by that attribute,
         * you can save space by disabling hash generation.
         *
         * @var string[]
         */
        private array $stringsNoHash = [],
    )
    {
    }

    public function toString(): string
    {
        $lines = [];

        if (count($this->fields)) {
            $lines[] = sprintf(
                'columnar_attrs = %s',
                implode(', ', $this->fields)
            );
        }

        if (count($this->stringsNoHash)) {
            $lines[] = sprintf(
                'columnar_strings_no_hash = %s',
                implode(', ', $this->stringsNoHash)
            );
        }

        return implode("\n", $lines);
    }
}
