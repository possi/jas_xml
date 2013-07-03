<?php

namespace jas\xml\Meta;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Attribute extends Annotation {
    public $parentAware = null;
    /*
    public function toDefinition() {
        return array(
        );
    }*/
}
