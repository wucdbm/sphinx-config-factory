<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Index\Distributed\Remote;

use Wucdbm\Sphinx\ConfigFactory\Config\ConfigPart;

enum RemoteTableConn: string implements ConfigPart
{
    case pconn = 'pconn';
    case agent_persistent = 'agent_persistent';

    public function toString(): string
    {
        return sprintf('conn=%s', $this->value);
    }
}
