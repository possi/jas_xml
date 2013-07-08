<?php

namespace jas\xml\Meta;
use jas\xml\Definition\Property;
use jas\xml\Definition\Property\AttributeNode;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Attribute extends Annotation {
    //public $type = null;
    public $name = null;
    
    public function defineProperty(Property $prop) {
        $an = new AttributeNode();
        $prop->setTypeDefinition($an);
        if ($this->name)
            $an->setName($this->name);
    }
}
