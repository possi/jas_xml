<?php

namespace jas\xml\Meta;
use jas\xml\Definition\Property;
use jas\xml\Definition\Klass;
use jas\xml\MetaDataException;
use Doctrine\Common\Annotations\Annotation as DoctrineAnnotation;

class Annotation extends DoctrineAnnotation {
    public function getName() {
        $c = get_class($this);
        if (strpos($c, __NAMESPACE__) === 0)
            $c = substr($c, strlen(__NAMESPACE__) + 1);
        return $c;
    }
    public function defineKlass(Klass $klass) {
        // This check is also done by @Target-Limitation of Doctrine
        // but to have the defineMethods generally, we define both here
        throw new MetaDataException("The Annotation '{$this->getName()}' isn't implemented to be defined on Class-Level");
    }
    public function defineProperty(Property $klass) {
        throw new MetaDataException("The Annotation '{$this->getName()}' isn't implemented to be defined on Property-Level");
    }
    public function isSingleAnnotation() {
        return true;
    }
}