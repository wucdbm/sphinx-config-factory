<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;
use Wucdbm\Sphinx\ConfigFactory\Config\Index\Distributed\LocalTable;
use Wucdbm\Sphinx\ConfigFactory\Config\Index\Distributed\RemoteTable;
use Wucdbm\Sphinx\ConfigFactory\ConfigHelper;

readonly class ManticoreDistributedIndex implements ConfigPart
{
    /** @var TargetIndex[] */
    private array $indices;

    public function __construct(
        private string $name,
        RemoteTable|LocalTable ...$indices,
    )
    {
        $this->indices = $indices;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        $str = ConfigHelper::indent(
            1,
            implode(
                "\n",
                array_map(
                    static fn(RemoteTable|LocalTable $index) => $index->toString(),
                    $this->indices
                )
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
