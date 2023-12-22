<?php

namespace Wucdbm\Sphinx\ConfigFactory;

class ConfigHelper
{
    public static function indent(int $times, string $string): string
    {
        $spaces = $times * 4;
        $lines = explode("\n", $string);

        return implode(
            "\n",
            array_map(
                static function ($line) use ($spaces) {
                    return str_repeat(' ', $spaces) . $line;
                },
                $lines
            )
        );
    }

    public static function terminateLines(string $lines, string $with = " \\\n"): string
    {
        return self::terminateLinesArray(explode("\n", $lines), $with);
    }

    public static function terminateLinesArray(array $lines, string $with = " \\\n"): string
    {
        return implode(
            $with,
            $lines
        );
    }

    public function printConfig(string $type, array $config, array $keys, $cleanup = false): string
    {
        if ($cleanup) {
            $config = $this->cleanupConfig($config, $keys);
        }

        $lines = [];
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $lines[] = ConfigHelper::indent(1, sprintf('%s = %s', $key, $item));
                }
            } else {
                $lines[] = ConfigHelper::indent(1, sprintf('%s = %s', $key, $value));
            }
        }

        $configString = implode("\n", $lines);

        return <<<EOF
{$type}
{
{$configString}
}
EOF;
    }

    public static function cleanupConfig(array $config, array $keys): array
    {
        return array_intersect_key(
            $config,
            array_intersect_key(array_fill_keys($keys, null), $config),
        );
    }
}
