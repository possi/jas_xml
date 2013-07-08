<?php

namespace jas\xml\Definition;
use jas\xml\Definition\Klass\KlassTypeDefinition;
use jas\xml\Definition\Klass\Document;
use jas\xml\MetaDataException;

class Klass extends GenericXmlDefinition implements \Serializable {
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
        parent::__construct();
        $this->class = $class;
    }
    public function getName() {
        return $this->class;
    }
    public function getType() {
        if ($this->type instanceof Document)
            return self::TYPE_DOCUMENT;
        else
            return self::TYPE_NODE;
    }
    public function setTypeDefinition(KlassTypeDefinition $def) {
        $this->type = $def;
    }
    /**
     * @return KlassTypeDefinition
     */
    public function getTypeDefinition() {
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
    public function getParent() {
        return $this->_parent;
    }
    
    public function serialize() {
        $d = array_merge(parent::serialize(false), get_object_vars($this));
        unset($d['_parent']);
        return serialize($d);
    }
    public function unserialize($serialized) {
        $d = is_string($serialized) ? unserialize($serialized) : $serialized;
        parent::unserialize($d);
        foreach (get_class_vars(__CLASS__) as $key) {
            if (array_key_exists($d[$key]))
                $this->{$key} = $d[$key];
        }
        foreach ($this->properties as $a) {
            /* @var $a Attribute */
            $a->_setParent($this);
        }
    }
}