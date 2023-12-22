<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index\Distributed\Remote;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;

final readonly class TableConnection implements ConfigPart
{
    /** @var string[] */
    public array $indices;

    public function __construct(
        public string $ip,
        public string $port,
        string ...$indices
    )
    {
    }

    public function toString(): string
    {
        return sprintf(
            '%s:%s:%s',
            $this->ip,
            $this->port,
            implode(',', $this->indices),
        );
    }
}
