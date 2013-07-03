<?php

namespace jas\xml\Meta;

/**
 * @Annotation
 * @Target({"CLASS","PROPERTY"})
 */
class Element extends Annotation {
    public $nodeName = null;
    public $abstractType = false;
    public $typeNS = null;
    public $type = null;
    /*
    public function toDefinition() {
        return array(
            'type' => $this->type,
            'name' => $this->nodeName,
            'atype' => $this->abstractType,
            'ns' => $this->typeNS,
        );
    }*/
}
