<?php

namespace jas\xml\Definition\Property;

class ValueNode implements PropertyTypeDefinition {
    private $c;
    
    public function getForceCDATA() {// TODO: Implement function
        return $this->c;
    }
    
    public function setForceCDATA($c) {
        $this->c = $c;
    }
}
