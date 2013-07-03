<?php

namespace jas\xml;

use jas\xml\Meta\Option;

use jas\xml\Helper\Helper;
use jas\xml\Accessor\ReflectionAccessor;
use jas\xml\Definition\Property;
use jas\xml\Definition\Klass;
use jas\xml\Helper\MetaStorage;

class Writer {
    protected $object;
    public function __construct($object) {
        $this->object = $object;
    }
    public function __destruct() {
        MetaStorage::destruct();
        Helper::clear();
    }
    public function getDOM() {
        $m = MetaStorage::getInstance()->getMeta($this->object);
        if ($m->getType() != Klass::TYPE_DOCUMENT)
            throw new Exception("The Object have to be a @Xml\Document");
        
        $version = $m->getOption('version', '1.0');
        $encoding = $m->getOption('encoding', 'utf-8');
        $dom = new \DOMDocument($version, $encoding);
        $dom->formatOutput = $m->getOption(Option::FORMAT_OUTPUT, false);
        $dom->preserveWhiteSpace = $m->getOption(Option::PRESERVE_WHITE_SPACE, false);
        
        $root_name = $m->getOption('rootNode', 'root');
        $root = $dom->createElement($root_name);
        $dom->appendChild($root);
        
        foreach ($m->getOption('attribs', array()) as $attrib => $aval) {
            $root->setAttribute($attrib, $aval);
        }
        
        $this->addProperties($root, $m, $this->object);
        
        return $dom;
    }
    
    protected function addProperties(\DOMElement $node, Klass $klass, $object) {
        $access = Helper::getAccessor($klass, $object);
        foreach ($klass->getProperties() as $prop) {
            /* @var $prop Property */
            if ($prop->getType() == Property::TYPE_ATTRIBUTE) {
                $node->setAttribute($prop->getName(), $access->get($prop->getName()));
            } else {
                $value = $access->get($prop->getName());
                if ($value !== null) {
                    if ($prop->isCollection()) {
                        if (!is_array($value) && !($value instanceof \Traversable))
                            throw new Exception("Invalid @Xml\Collection-Value: ".gettype($value).(is_object($value)?" ".get_class($value):""));
                        $node_name = $prop->getOption('nodeName', $prop->getName());
                        $child = $node->ownerDocument->createElement($node_name);
                        foreach ($value as $element) {
                            $this->valueNode($prop, $klass, $child, $element);
                        }
                        $node->appendChild($child);
                    } else {
                        $this->valueNode($prop, $klass, $node, $value);
                    }
                }
            }
        }
    }
    protected function valueNode(Property $prop, Klass $klass, \DOMElement $node, $value) {
        if (is_object($value) && ($m = MetaStorage::getInstance()->getMeta($value)) != false) {
            $m->setParent($klass);
            $node_name = $prop->getOption('nodeName', $m->getOption('nodeName', $prop->getName()));
            $child = $node->ownerDocument->createElement($node_name);
            $this->addProperties($child, $m, $value);
            $node->appendChild($child);
        } else {
            $node_name = $prop->getOption('nodeName', $prop->getName());
            $child = $node->ownerDocument->createElement($node_name);
            $value = Helper::getNormalizer($klass)->valueToString($value);
            $child->nodeValue = $value;
            $node->appendChild($child);
        }
    }
    
    public function getXML() {
        return $this->getDOM()->saveXML();
    }
    public static function toDOM($object) {
        $w = new self($object);
        return $w->getDOM();
    }
    public static function toXML($object) {
        $w = new self($object);
        return $w->getXML();
    }
}