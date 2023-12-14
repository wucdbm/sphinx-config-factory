<?php

namespace Wucdbm\Sphinx\ConfigFactory\DTO;

use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class ManticoreIndex implements ConfigPart
{
    public function __construct(
        private string $name,
        private ManticoreSource $source,
        private string $storage,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toString(
    ): string
    {
        $sourceLine = sprintf(
            'source = %s',
            $this->source->getName()
        );
        $pathLine = sprintf(
            'path = %s',
            $this->storage.DIRECTORY_SEPARATOR.$this->name
        );

        $lines = [
            $sourceLine,
            $pathLine,
            'min_word_len            = 2',
            'min_prefix_len          = 2, max_substring_len = 6',
//            'min_prefix_len          = 2',
//            'max_substring_len = 6',
        ];

        $config = ConfigHelper::indent(1, implode("\n", $lines));

        return <<<EOF
index {$this->name}
{
{$config}
}
EOF;
    }
}
