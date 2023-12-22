<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index\Distributed;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;

final readonly class LocalTable implements ConfigPart
{
    /** @var string[] */
    private array $names;

    public function __construct(
        string ...$names
    )
    {
        $this->names = $names;
    }

    public function toString(): string
    {
        return sprintf(
            'local = %s',
            implode(', ', $this->names)
        );
    }
}
