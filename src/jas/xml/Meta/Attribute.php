<?php

namespace jas\xml\Meta;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Attribute extends Annotation {
    public $type = null;
    public $name = null;
    /*
    public function toDefinition() {
        return array(
            'type' => $this->type,
            'name' => $this->name,
        );
    }*/
}
