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

    public static function fromUrl(string $url): self
    {
        $db = parse_url($url);

        return new self(
            $db['scheme'],
            $db['host'],
            $db['port'],
            ltrim($db['path'], '/'),
            $db['user'],
            $db['pass'],
        );
    }
}
