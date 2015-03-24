<?php

namespace jas\xml;

use jas\xml\Meta\Option;
use jas\xml\Helper\Helper;
use jas\xml\Accessor\ReflectionAccessor;
use jas\xml\Definition\Property;
use jas\xml\Definition\Klass;
use jas\xml\Helper\MetaStorage;
use Symfony\Component\Config\Definition\Exception\Exception;

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
            throw new Exception("The Object have to be a @Xml\\Document");
        /* @var $m \jas\xml\Definition\Klass */
        
        $mtd = $m->getTypeDefinition();
        
        $dom = new \DOMDocument($mtd->getVersion(), $mtd->getEncoding());
        $dom->formatOutput = $m->getOption(Option::FORMAT_OUTPUT, false);
        $dom->preserveWhiteSpace = $m->getOption(Option::PRESERVE_WHITE_SPACE, false);
        
        
        $rn = $mtd->getRootNode();
        $root = $dom->createElement($rn->getName());
        $dom->appendChild($root);
        foreach ($rn->getAttributes() as $attrib => $aval) {
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
            } elseif ($prop->getType() == Property::TYPE_VALUE) {
                $value = Helper::getNormalizer($prop)->valueToString($access->get($prop->getName()));
                $node->nodeValue = $value;
            } else {
                $value = $access->get($prop->getName());
                if ($value !== null) {
                    if ($prop->getCollection()) {
                        if (!is_array($value) && !($value instanceof \Traversable))
                            throw new Exception("Invalid @Xml\\Collection-Value: ".gettype($value).(is_object($value)?" ".get_class($value):""));
                        if ($prop->getType() == Property::TYPE_FRAGMENT) {
                            $child = $node->ownerDocument->createDocumentFragment();
                        } else {
                            $node_name = $prop->getTypeDefinition()->getName();
                            if (empty($node_name))
                                $node_name = $prop->getName();
                            $child = $node->ownerDocument->createElement($node_name);
                        }
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
    protected function valueNode(Property $prop, Klass $klass, \DOMNode $node, $value) {
        if (is_object($value) && ($m = MetaStorage::getInstance()->getMeta($value)) != false) {
            $m->setParent($klass);
            if ($m->getTypeDefinition() instanceof Klass\ElementNode)
                $node_name = $m->getTypeDefinition()->getName();
            else
                $node_name = $prop->getTypeDefinition()->getName();
            if (empty($node_name))
                $node_name = $prop->getName();
            if (empty($node_name))
                throw new Exception("Object ".get_class($value)." as Element of Document Fragment has no name defined.");
            $child = $node->ownerDocument->createElement($node_name);
            if (!$prop->getDataType() || !is_a($value, $prop->getDataType())) {
                if (($type = $prop->getTypeForClass($class = get_class($value))) != null) {
                    $child->setAttribute($prop->getOption('typeAttributeName', 'type'), $type);
                } else {
                    $child->setAttribute($prop->getOption('classAttributeName', 'class'), $class);
                }
            }
            $this->addProperties($child, $m, $value);
            $node->appendChild($child);
        } else {
            $node_name = $prop->getTypeDefinition()->getName();
            if (empty($node_name))
                $node_name = $prop->getName();
            $child = $node->ownerDocument->createElement($node_name);
            $value = Helper::getNormalizer($prop)->valueToString($value);
            $child->nodeValue = htmlspecialchars($value);
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