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

    public static function terminateLines(string $lines, string $with = " \\"): string
    {
        return self::terminateLinesArray(explode("\n", $lines), $with);
    }

    public static function terminateLinesArray(array $lines, string $with = " \\"): string
    {
        return implode(
            $with,
            array_map(
                static function (string $line) use ($with) {
                    return $line . $with;
                },
                $lines
            )
        );
    }
}
