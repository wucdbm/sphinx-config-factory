<?php

namespace Wucdbm\Sphinx\ConfigFactory\DTO;

readonly class SqlAttr implements ConfigPart
{
    public function __construct(
        private string $field,
        private SqlAttrType $type,
    )
    {
    }

    public function toString(): string
    {
        return sprintf(
            '%s = %s',
            $this->type->getAttr(),
            $this->field
        );
    }
}
