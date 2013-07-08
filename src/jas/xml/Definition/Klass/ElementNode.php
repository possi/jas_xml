<?php

namespace jas\xml\Definition\Klass;

class ElementNode implements KlassTypeDefinition {
    private $n;
    
    public function getName() {
        return $this->n;
    }
    
    public function setName($n) {
        $this->n = $n;
    }
    
}
