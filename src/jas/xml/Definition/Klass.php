<?php

namespace jas\xml\Definition;
use jas\xml\MetaDataException;

class Klass extends Generic implements \Serializable {
    const TYPE_DOCUMENT = 1;
    const TYPE_NODE = 2;
    
    private $_parent = null;
    private $class;
    private $properties = array();
    private $type;
    
    /**
     * @param string $class
     */
    public function __construct($class) {
        $this->class = $class;
    }
    public function parse($an) {
        parent::parse($an);
        if (isset($an['Document'])) {
            $d = $an['Document'];
            if ($d->version)
                $this->options['version'] = $d->version;
            if ($d->enc)
                $this->options['encoding'] = $d->encoding;
            if ($d->rootNode)
                $this->options['rootNode'] = $d->rootNode;
            if ($d->attribs)
                $this->options['attribs'] = $d->attribs;
            $this->type = self::TYPE_DOCUMENT;
        } elseif (isset($an['Element'])) {
            $e = $an['Element'];
            if ($e->nodeName)
                $this->options['nodeName'] = $e->nodeName;
            $this->type = self::TYPE_NODE;
        } else {
            throw new MetaDataException("Class $this->class have to be either a @Xml\Document or @Xml\Element");
        }
    }
    public function getName() {
        return $this->class;
    }
    public function getType() {
        return $this->type;
    }
    public function addProperty(Property $a) {
        $this->properties[$a->getName()] = $a;
    }
    public function getProperties() {
        return $this->properties;
    }
    
    /**
     * Sets the Parent-Klass-Definition, depending on the current XML-Tree, to pass parent options.
     * @param Klass $parent
     */
    public function setParent(Klass $parent) {
        $this->_parent = $parent;
    }
    public function getOption($opt, $default = null) {
        if ($this->_parent)
            return parent::getOption($opt, $this->_parent->getOption($opt, $default));
        else
            return parent::getOption($opt, $default);
    }
    public function getOptions() {
        return array_merge($this->_parent->getOptions(), parent::getOptions());
    }
    
    public function serialize() {
        return serialize(array(
            'class' => $this->class,
            'type' => $this->type,
            'options' => $this->options,
            'properties' => $this->properties,
        ));
    }
    public function unserialize($serialized) {
        $data = unserialize($serialized);
        $this->class = $data['class'];
        $this->type = $data['type'];
        $this->options = $data['options'];
        $this->properties = $data['properties'];
        foreach ($this->properties as $a) {
            /* @var $a Attribute */
            $a->_setParent($this);
        }
    }
}