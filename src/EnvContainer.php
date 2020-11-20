<?php

/*
 * This file is part of the wucdbm/sphinx-config-factory package.
 *
 * Copyright (c) Martin Kirilov <wucdbm@gmail.com>.
 *
 * Author Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wucdbm\Sphinx\ConfigFactory;

class EnvContainer {

    private array $env;

    public function __construct(array $env) {
        $this->env = $env;
    }

    public function get(string $key): ?string {
        return $this->env[$key] ?? null;
    }

    public function has(string $key): ?string {
        return isset($this->env[$key]);
    }
}
