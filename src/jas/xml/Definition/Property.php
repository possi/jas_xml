<?php

namespace jas\xml\Definition;

use jas\xml\MetaDataException;

class Property extends Generic implements \Serializable {
    const TYPE_ATTRIBUTE = 1;
    const TYPE_ELEMENT = 2;
    
    private $_parent;
    private $name;
    private $type;
    private $collection;
    private $data_type = null;
    
    public function __construct(Klass $klass, $name) {
        $this->_parent = $klass;
        $this->name = $name;
    }
    public function getName() {
        return $this->name;
    }
    public function getType() {
        return $this->type;
    }
    public function isCollection() {
        return $this->collection != false;
    }
    public function getCollectionType() {
        return ($this->collection && $this->collection !== true) ? $this->collection : $this->getDataType();
    }
    public function parse($an) {
        if (isset($an['Attribute'])) {
            $this->type = self::TYPE_ATTRIBUTE;
        } elseif (isset($an['Element'])) {
            $e = $an['Element'];
            if ($e->nodeName)
                $this->options['nodeName'] = $e->nodeName;
            $this->type = self::TYPE_ELEMENT;
        } else {
            throw new MetaDataException("Class-Property {$this->getKlass()->getName()}::{$this->name} have to be either a @Xml\Attribute or @Xml\Element");
        }
        if (isset($an['Collection'])) {
            $c = $an['Collection'];
            $this->collection = $c->type ? $c->type : true;
        }
    }
    
    public function serialize() {
        return serialize(array(
            'name' => $this->name,
            'type' => $this->type,
            'options' => $this->options,
            'collection' => $this->collection,
            'data_type' => $this->data_type,
        ));
    }
    public function unserialize($serialized) {
        $data = unserialize($serialized);
        $this->name = $data['name'];
        $this->type = $data['name'];
        $this->options = $data['options'];
        $this->collection = $data['collection'];
        $this->data_type = $data['data_type'];
    }
    public function getKlass() {
        return $this->_parent;
    }
    /**
     * Used to update parent-assoc after unserialization
     * @protected Only to use from same NameSpace
     * @param Klass $klass
     */
    public function _setParent(Klass $klass){
        $this->_parent = $klass;
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
}