<?php

namespace jas\xml;

use jas\xml\Definition\Klass;
use jas\xml\Definition\Property;
use jas\xml\Helper\MetaStorage;
use jas\xml\Helper\Helper;

class Reader {
    private $target;
    private $object = null;
    public function __construct($target) {
        $this->target = $target;
    }
    public function parseXML($xml) {
        $dom = new \DOMDocument;
        $dom->loadXML($xml);
        return $this->parseDOM($dom);
    }
    public function parseDOM(\DOMNode $dom) {
        $this->prepareTarget($dom);
        $m = MetaStorage::getInstance()->getMeta($this->object);
        $this->getProperties($dom->documentElement, $m, $this->object);
        return $this->getData();
    }
    protected function getProperties(\DOMElement $node, Klass $klass, $object) {
        $access = Helper::getAccessor($klass, $object);
        foreach ($klass->getProperties() as $prop) {
            /* @var $prop Property */
            if ($prop->getType() == Property::TYPE_ATTRIBUTE) {
                if ($node->hasAttribute($prop->getName()))
                    $access->set($prop->getName(), $node->getAttribute($prop->getName()));
            } elseif ($prop->getType() == Property::TYPE_VALUE) {
                $value = Helper::getNormalizer($prop)->stringToValue($node->nodeValue);
                $access->set($prop->getName(), $value);
            } else {
                if ($prop->getCollection()) {
                    if ($node->childNodes->length > 0) {
                        $list = $access->get($prop->getName());
                        if ($list == null) {
                            $list = array();
                        } elseif (!is_array($list) && (!is_object($list) || !($list instanceof \ArrayAccess))) {
                            throw new Exception("Invalid @Xml\Collection-Value: ".gettype($value).(is_object($value)?" ".get_class($value):"")." has to be Array or instanceof ArrayAccess");
                        }
                        foreach ($node->childNodes as $child) {
                            /* @var $child \DOMNode */
                            if ($child->nodeType == \XML_ELEMENT_NODE) {
                                $value = $this->getValue($child, $prop);
                                $list[] = $value;
                            }
                        }
                        if (is_array($list)) // no need if is Collection-Object, as they are byRef
                            $access->set($prop->getName(), $list);
                    }
                } else {
                    $value = $this->getValue($node, $prop);
                    $access->set($prop->getName(), $value);
                }
            }
        }
    }
    protected function getValue(\DOMElement $node, Property $prop) {
        if ($prop->getDataType() && !Helper::isPrimitive($prop->getDataType())) {
            if ($node->hasAttribute($cAN = $prop->getOption('classAttributeName', 'class'))) {
                $type = $node->getAttribute($cAN);
            } elseif ($node->hasAttribute($tAN = $prop->getOption('typeAttributeName', 'type'))) {
                $type = $prop->getClassForType($node->getAttribute($name));
            } else {
                $type = $prop->getDataType();
            }
            if (empty($type))
                throw new ProcessingException("There is no element type (class) for the node '{$node->nodeName}' in line {$node->getLineNo()} defined");
            $type = new $type;
            $this->getProperties($node, MetaStorage::getInstance()->getMeta($type), $type);
            return $type;
        } else {
            return Helper::getNormalizer($prop)->stringToValue($node->nodeValue);
        }
    }
    
    protected function prepareTarget($dom) {
        if (!$this->object) {
            if (empty($this->target)) {
                $node = $dom instanceof \DOMDocument ? $dom->documentElement : $dom;
                $class = $nod->getAttribute('class');
                if (empty($class))
                    throw new Exception('No Target-Class given to load XML into');
                $this->object = new $class;
            } elseif (is_string($this->target)) {
                $this->object = new $this->target();
            } else {
                $this->object = $this->target;
                unset($this->target);
            } 
        }
        return $this->object;
    }
    
    public function getData() {
        return $this->object;
    }
    
    public static function fromXML($target, $xml = null) {
        if (empty($xml)) {
            $xml = $target;
            $target = null;
        }
        $m = new self($target);
        $m->parseXML($xml);
        return $m->getData();
    }
}