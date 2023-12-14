<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config;

use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class ManticoreDistributedIndex implements ConfigPart
{
    /** @var TargetIndex[] */
    private readonly array $indices;

    public function __construct(
        private string $name,
        TargetIndex ...$indices,
    )
    {
        $this->indices = $indices;
    }

    public function toString(): string
    {
        $str = ConfigHelper::indent(
            1,
            implode(
                "\n",
                array_map(static function (TargetIndex $index) {
                    return sprintf(
                        'agent = %s:%s:%s',
                        $index->ip,
                        $index->port,
                        $index->index->getName(),
                    );
                }, $this->indices)
            )
        );

        $indices = implode(
            "\n\n",
            array_map(
                fn(TargetIndex $index) => $index->index->toString(),
                $this->indices
            )
        );

        return <<<EOF
{$indices}

index {$this->name}
{
    type = distributed
{$str}
}
EOF;
    }
}
