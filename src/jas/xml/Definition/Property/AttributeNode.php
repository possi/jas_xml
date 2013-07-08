<?php

namespace jas\xml\Definition\Property;

class AttributeNode implements PropertyTypeDefinition {
    private $n;
    
    public function getName() {
        return $this->n;
    }
    
    public function setName($n) {
        $this->n = $n;
    }
}