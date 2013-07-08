<?php

namespace jas\xml\Meta;
use jas\xml\MetaDataException;
use jas\xml\Definition\Klass\ElementNode as KlassElementNode;
use jas\xml\Definition\Property\ElementNode as PropertyElementNode;
use jas\xml\Definition\Property;
use jas\xml\Definition\Klass;

/**
 * @Annotation
 * @Target({"CLASS","PROPERTY"})
 */
class Element extends Annotation {
    public $nodeName = null;
    public $abstractType = false;
    public $typeNS = null;
    public $type = null;

    public function defineKlass(Klass $klass) {
        $en = new KlassElementNode();
        $klass->setTypeDefinition($en);
        if ($this->nodeName)
            $en->setName($this->nodeName);
        
        if ($this->abstractType || $this->type || $this->typeNS)
            throw new MetaDataException("A class @Xml\Element can't have a type. It is the type of the class");
    }
    public function defineProperty(Property $prop) {
        $en = new PropertyElementNode();
        $prop->setTypeDefinition($en);
        if ($this->nodeName)
            $en->setName($this->nodeName);
        
        if ($this->type)
            $prop->setDataType($this->type);
        
        if ($this->abstractType || $this->typeNS)
            throw new MetaDataException("Not yet implemented");
    }
}
