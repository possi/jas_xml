<?php

namespace jas\xml\Meta;
use jas\xml\Definition\Property;
use jas\xml\Definition\Property\Collection as CollectionDefinition;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Collection extends Annotation {
    public $type = null;
    
    public function defineProperty(Property $prop) {
        $cd = new CollectionDefinition();
        $prop->setCollection($cd);
        
        if ($this->type)
            //$cd->setType($this->type);
            $prop->setDataType($this->type);
    }
}
