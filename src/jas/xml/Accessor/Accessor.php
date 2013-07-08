<?php

namespace jas\xml\Accessor;

interface Accessor {
    public function __construct($object);
    public function get($property);
    public function set($property, $value);
    public function callEvent($method, $eventObj);
}