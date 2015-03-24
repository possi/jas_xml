<?php

namespace jas\xml\Definition\Klass;

class RootNode {
    private $n;
    private $a = array();
    
    public function getName() {
        return $this->n;
    }
    
    public function setName($n) {
        $this->n = $n;
    }
    
    public function getAttributes() {
        return $this->a;
    }
    
    public function setAttributes(array $a) {
        $this->a = $a;
    }
    
    public function addAttribute($name, $value) {
        $this->a[$name] = $value;
    }
    public function addAttributes(array $values) {
        foreach ($values as $k => $v) {
            $this->addAttribute($k, $v);
        }
    }
}
