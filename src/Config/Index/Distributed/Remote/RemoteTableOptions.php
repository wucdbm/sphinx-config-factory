<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index\Distributed\Remote;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;

final readonly class RemoteTableOptions implements ConfigPart
{
    public function __construct(
        public int $retryCount,
        public ?HaStrategy $haStrategy,
        public ?Conn $conn,
        public ?Blackhole $blackhole,
    )
    {
    }

    public function toString(): string
    {
        $items = [];

        if ($this->retryCount) {
            $items[] = sprintf('retry_count=%d', $this->retryCount);
        }

        if ($this->haStrategy) {
            $items[] = $this->haStrategy->toString();
        }

        if ($this->conn) {
            $items[] = $this->conn->toString();
        }

        if ($this->blackhole) {
            $items[] = $this->blackhole->toString();
        }

        if (!count($items)) {
            return '';
        }

        return sprintf(
            '[%s]',
            implode(',', $items)
        );
    }
}
