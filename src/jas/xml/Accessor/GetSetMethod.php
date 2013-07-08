<?php

namespace jas\xml\Accessor;

class GetSetMethod implements Accessor {
    private $o;
    public function __construct($object) {
        $this->o = $object;
    }
    public function get($property) {
        return call_user_func(array($this->o, 'get'.str_replace("_", "", $property)));
    }
    public function set($property, $value) {
        return call_user_func(array($this->o, 'set'.str_replace("_", "", $property)), $value);
    }
    public function callEvent($method, $eventObj) {
        return call_user_func(array($this->o, $method), $eventObj);
    }
}