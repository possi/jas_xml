<?php

namespace jas\xml\Meta;
use jas\xml\Definition\Klass\Document as DocumentDefinition;
use jas\xml\Definition\Klass\RootNode;
use jas\xml\MetaDataException;
use jas\xml\Definition\Klass;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Document extends Annotation {
    public $version = null;
    public $enc = null;
    public $rootNode = null;
    public $attribs = array();
    
    public function defineKlass(Klass $def) {
        if ($def->getTypeDefinition() != null && !($def->getTypeDefinition() instanceof DocumentDefinition))
            throw new MetaDataException("Class {$def->getName()} can't be element and document at once.");
        if ($def->getTypeDefinition() == null)
            $def->setTypeDefinition(new DocumentDefinition());
        $dd = $def->getTypeDefinition();
        /* @var $dd jas\xml\Definition\Klass\Document */
        if ($this->version)
            $dd->setVersion($this->version);
        if ($this->enc)
            $dd->setEncoding($this->enc);
        
        if (!$dd->getRootNode())
            $dd->setRootNode(new RootNode());
        $rn = $dd->getRootNode();
        
        if ($this->rootNode)
            $rn->setName($this->rootNode);
        if (count($this->attribs))
            $rn->addAttributes($this->attribs);
        
        if (!$rn->getName())
            throw new MetaDataException("A rootNode-Name has to be defined");
    }
}
