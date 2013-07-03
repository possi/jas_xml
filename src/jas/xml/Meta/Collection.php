<?php

namespace jas\xml\Meta;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Collection extends Annotation {
    public $type = null;
    /*
    public function toDefinition() {
        return array(
            'type' => '__xmlList',
            'List*type' => $this->type,
        );
    }*/
}
