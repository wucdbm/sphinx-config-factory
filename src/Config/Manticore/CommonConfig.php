<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Manticore;

readonly class CommonConfig extends AbstractManticoreConfig
{
    public function __construct(array $config)
    {
        parent::__construct('common', $config);
    }

    /**
     * @return string[]
     */
    protected function getAllowedKeys(): array
    {
        return [
            'lemmatizer_base',
            'on_json_attr_error',
            'json_autoconv_numbers',
            'json_autoconv_keynames',
            'plugin_dir',
        ];
    }
}
