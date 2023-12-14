<?php

namespace Wucdbm\Sphinx\ConfigFactory\DTO;

use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class DistributedIndex implements ConfigPart
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

        return <<<EOF
index {$this->name}
{
    type = distributed
{$str}
}
EOF;
    }
}
