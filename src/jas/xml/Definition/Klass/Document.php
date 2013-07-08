<?php

namespace jas\xml\Definition\Klass;

class Document implements KlassTypeDefinition {
    private $v = "1.0";
    private $e = "utf-8";
    private $r;
    
    public function getVersion() {
        return $this->v;
    }
    
    public function setVersion($v) {
        $this->v = $v;
    }
    
    public function getEncoding() {
        return $this->e;
    }
    
    public function setEncoding($e) {
        $this->e = $e;
    }
    
    public function getRootNode() {
        return $this->r;
    }
    
    public function setRootNode(RootNode $r) {
        $this->r = $r;
    }
    
}
