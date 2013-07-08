<?php

namespace jas\xml\Meta;
use jas\xml\Definition\Property;
use jas\xml\Definition\Property\ValueNode;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Value extends Annotation {
    public function defineProperty(Property $prop) {
        $vn = new ValueNode();
        $prop->setTypeDefinition($vn);
    }
}
