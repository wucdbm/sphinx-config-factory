<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;
use Wucdbm\Sphinx\ConfigFactory\Config\Source\Source;
use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class Index implements ConfigPart
{
    public function __construct(
        private string $name,
        private Source $source,
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

        $source = str_replace(
            '$index_name',
            $this->name,
            $this->source->toString(),
        );

        return <<<EOF
{$source}

index {$this->name}
{
{$config}
}
EOF;
    }
}
