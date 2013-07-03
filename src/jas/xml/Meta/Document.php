<?php

namespace jas\xml\Meta;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Document extends Annotation {
    public $version = null;
    public $enc = null;
    public $rootNode = null;
    public $attribs = array();
    /*
    public function toDefinition() {
        if (empty($this->rootNode))
            throw new \de\jaschastarke\Exception('A XmlDocument needs an defined rootNode');
        return array(
            'version' => $this->version,
            'enc' => $this->enc,
            'rootNode' => $this->rootNode,
            'attribs' => $this->attribs,
        );
    }*/
}
