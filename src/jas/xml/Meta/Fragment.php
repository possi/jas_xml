<?php

namespace jas\xml\Meta;
use jas\xml\Definition\Property;


/**
 * WIP: Can only be used for writing, not reading
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Fragment extends Annotation {
    public function defineProperty(Property $prop) {
        $en = new Property\FragmentNode();
        $prop->setTypeDefinition($en);
    }
}
