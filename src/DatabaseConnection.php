<?php

namespace Wucdbm\Sphinx\ConfigFactory;

final readonly class DatabaseConnection
{
    public function __construct(
        public string $type,
        public string $host,
        public string $port,
        public string $database,
        public string $username,
        public string $password
    ) {
    }
}
