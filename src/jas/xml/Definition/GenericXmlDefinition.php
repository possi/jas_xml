<?php

namespace jas\xml\Definition;

abstract class GenericXmlDefinition extends Definition implements \Serializable {
    private $typemap = null;
    public function getTypeMap() {
        return $this->typemap;
    }
    public function setTypeMap(array $map) {
        $this->typemap = $map;
    }
    public function getTypeForClass($class) {
        if ($this->typemap && in_array($class, $this->typemap))
            return array_search($class, $this->typemap);
        elseif ($this->getParent())
            return $this->getParent()->getTypeForClass($class);
        return null;
    }
    public function getClassForType($type) {
        if ($this->typemap && isset($this->typemap[$type]))
            return $this->typemap[$type];
        elseif ($this->getParent())
            return $this->getParent()->getClassForType($class);
        return null;
    }
    
    public function serialize($to_string = true) {
        $s = get_object_vars($this);
        return $to_string ? serialize($s) : $s;
    }
    public function unserialize($str) {
        $d = is_string($str) ? unserialize($str) : $str;
        foreach (get_class_vars(__CLASS__) as $key) {
            if (array_key_exists($d[$key]))
                $this->{$key} = $d[$key];
        }
    }
}