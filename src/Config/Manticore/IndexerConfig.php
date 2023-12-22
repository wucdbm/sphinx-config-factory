<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Manticore;

readonly class IndexerConfig extends AbstractManticoreConfig
{
    public function __construct(array $config)
    {
        parent::__construct('indexer', $config);
    }

    /**
     * @return string[]
     */
    protected function getAllowedKeys(): array
    {
        return [
            'mem_limit',
            'max_iops',
            'max_iosize',
            'max_xmlpipe2_field',
            'write_buffer',
            'max_file_field_buffer',
            'on_file_field_error',
            'lemmatizer_cache',
        ];
    }
}
