<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index\Distributed;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;
use Wucdbm\Sphinx\ConfigFactory\Config\Index\Distributed\Remote\RemoteTableConnection;
use Wucdbm\Sphinx\ConfigFactory\Config\Index\Distributed\Remote\RemoteTableOptions;

final readonly class RemoteTable implements ConfigPart
{
    /** @var string[] */
    public array $indices;

    public function __construct(
        public ?RemoteTableOptions $options,
        RemoteTableConnection ...$indices
    )
    {
    }

    public function toString(): string
    {
        return sprintf(
            'agent = %s%s',
            implode(
                '|',
                array_map(
                    static fn(RemoteTableConnection $conn) => $conn->toString(),
                    $this->indices
                ),
            ),
            $this->options ? $this->options->toString() : ''
        );
    }
}
