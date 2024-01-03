<?php

namespace Wucdbm\Sphinx\ConfigFactory\Config\Attr;

use Wucdbm\Sphinx\ConfigFactory\Config\OrderableConfigPart;

readonly class SqlAttr implements OrderableConfigPart
{
    public function __construct(
        private string $field,
        private SqlAttrType $type,
    )
    {
    }

    public function getPriority(): int
    {
        return 100;
    }

    public static function fromArray(array $attrs): array
    {
        $attributes = [];

        /**
         * @var string $field
         * @var SqlAttrType $type
         */
        foreach ($attrs as $field => $type) {
            if (!($type instanceof SqlAttrType)) {
                throw new \RuntimeException(sprintf(
                    'Value for Attribute "%s" was expected to be "%s", "%s" given',
                    $field,
                    SqlAttrType::class,
                    get_debug_type($type)
                ));
            }

            $attributes[] = new self($field, $type);

        }

        return $attributes;
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
