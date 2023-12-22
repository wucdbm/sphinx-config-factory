<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config;

final readonly class DatabaseConnection implements ConfigPart
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

    public function toString(): string
    {
        return <<<EOF
type                    = {$this->type}

sql_host                = {$this->host}
sql_port                = {$this->port}  # optional, default is 3306
sql_db                  = {$this->database}
sql_user                = {$this->username}
sql_pass                = {$this->password}
EOF;

        // TODO: Implement toString() method.
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
