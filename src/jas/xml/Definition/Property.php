<?php

namespace jas\xml\Definition;
use jas\xml\Definition\Property\ElementNode;
use jas\xml\Definition\Property\Collection;
use jas\xml\Definition\Property\PropertyTypeDefinition;
use jas\xml\Definition\Property\AttributeNode;
use jas\xml\Definition\Property\ValueNode;
use jas\xml\MetaDataException;

class Property extends GenericXmlDefinition implements \Serializable {
    const TYPE_ATTRIBUTE = 1;
    const TYPE_ELEMENT = 2;
    const TYPE_VALUE = 3;
    
    private $_parent;
    private $name;
    private $collection;
    private $type;
    private $data_type = null;
    
    public function __construct(Klass $klass, $name) {
        parent::__construct();
        $this->_parent = $klass;
        $this->name = $name;
    }
    public function getName() {
        return $this->name;
    }
    public function getType() {
        if ($this->type instanceof ValueNode)
            return self::TYPE_VALUE;
        elseif ($this->type instanceof ElementNode)
            return self::TYPE_ELEMENT;
        else
            return self::TYPE_ATTRIBUTE;
    }
    public function setTypeDefinition(PropertyTypeDefinition $def) {
        $this->type = $def;
    }
    /**
     * @return PropertyTypeDefinition
     */
    public function getTypeDefinition() {
        return $this->type;
    }
    public function setCollection(Collection $col) {
        $this->collection = $col;
    }
    public function getCollection() {
        return $this->collection;
    }
    /*public function getCollectionType() {
        return ($this->collection && $this->collection !== true) ? $this->collection->getType() : $this->getDataType();
    }*/
    public function setDataType($type) {
        $this->data_type = $type;
    }
    public function getDataType() {
        return $this->data_type;
    }
    
    public function getParent() {
        return $this->_parent;
    }
    public function getKlass() {
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
    
    /**
     * Used to update parent-assoc after unserialization
     * @protected Only to use from same NameSpace
     * @param Klass $klass
     */
    public function _setParent(Klass $klass){
        $this->_parent = $klass;
    }
}