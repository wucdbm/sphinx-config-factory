<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;
use Wucdbm\Sphinx\ConfigFactory\Config\Source\Source;
use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class Index implements ConfigPart
{
    private array $options;

    public function __construct(
        private string $name,
        private Source $source,
        private string $storage,
        array $options = [],
        private ?KillList $killList = null,
    )
    {
        $this->options = ConfigHelper::cleanupConfig($options, [
            'min_word_len',

            'min_prefix_len',
            'min_infix_len',

            'prefix_fields',
            'infix_fields',
            'max_substring_len',

            'columnar_attrs',
            'columnar_strings_no_hash',
        ]);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        $sourceLine = sprintf(
            'source = %s',
            $this->source->getName()
        );
        $pathLine = sprintf(
            'path = %s',
            $this->storage . DIRECTORY_SEPARATOR . $this->name
        );

        $lines = [
            $sourceLine,
            $pathLine,
        ];

        $lines[] = '';

        if ($this->killList) {
            $lines[] = $this->killList->toString();
            $lines[] = '';
        }

        foreach ($this->options as $option => $value) {
            $lines[] = sprintf(
                '%s = %s',
                $option,
                $value,
            );
        }

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
