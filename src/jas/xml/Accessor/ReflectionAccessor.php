<?php

namespace jas\xml\Accessor;

class ReflectionAccessor implements Accessor {
    private $o;
    /**
     * @var \ReflectionObject
     */
    private $r;
    public function __construct($object) {
        $this->o = $object;
        $this->r = new \ReflectionObject($object);
    }
    /** 
     * @param string $property
     * @return \ReflectionProperty
     */
    protected function prop($property) {
        foreach ($this->r->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED) as $p) {
            /* @var $p \ReflectionProperty */
            if (!$p->isStatic() && $p->getName() == $property) {
                $p->setAccessible(true);
                return $p;
            }
        }
        throw new AccessorException("Property ".get_class($this->o)."::$property not found. Have to be a non-dynamic, non-static, non-private class-property.");
    }
    public function get($property) {
        return $this->prop($property)->getValue($this->o);
    }
    public function set($property, $value) {
        return $this->prop($property)->setValue($this->o, $value);
    }
}
