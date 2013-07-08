<?php

namespace jas\xml\Helper;
use jas\xml\MetaDataException;
use jas\xml\Definition\Definition;
use jas\xml\Meta\Annotation;
use jas\xml\Definition\Property;
use jas\xml\Definition\Klass;
use jas\xml\Exception;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;

/**
 * @singleton
 */
final class MetaStorage {
    private static $inst = null;
    private function __construct() {}
    public static function getInstance() {
        if (!self::$inst)
            self::$inst = new self;
        return self::$inst;
    }
    public static function destruct() {
        if (self::$inst) {
            self::$inst->cache = array();
        }
        self::$inst = null;
    }
    
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader = null;
    public function setAnnotationReader(Reader $reader) {
        $this->reader = $reader;
    }
    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function getAnnotationReader() {
        if (!$this->reader) {
            /*$config = new \Doctrine\ORM\Configuration();
            $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ApcCache());
            $annotdriver = $config->newDefaultAnnotationDriver(array(self::META_NS));
            $this->reader = $annotdriver->getReader();*/
            // TODO: Implement a cached AnnotationReader
            $this->reader = new AnnotationReader();
        }
        return $this->reader;
    }
    private function a() {
        return $this->getAnnotationReader();
    }
    
    private $cache = array();
    
    /**
     * @param object $object
     * @throws Exception If Annotation-Configuration is invalid
     * @return Definition
     */
    public function getMeta($object) {
        $class = get_class($object);
        if (!isset($this->cache[$class])) {
            $rclass = new \ReflectionObject($object);
            try {
                $annotations = self::annotList($t=$this->a()->getClassAnnotations($rclass));
                
                $def = new Klass($class);
                $this->parseDefinition($def, $annotations);
                
                foreach ($rclass->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED) as $prop) {
                    /* @var $prop \ReflectionProperty */
                    if ($prop->isStatic())
                        continue;
                    try {
                        $property_annotations = self::annotList($this->a()->getPropertyAnnotations($prop));
                        if (count($property_annotations) == 0)
                            continue;
                        $pdef = new Property($def, $prop->getName());
                        $this->parseDefinition($pdef, $property_annotations);
                        $def->addProperty($pdef);
                    } catch (MetaDataException $e) {
                        throw new Exception("Failed to get MetaData for Class-Property: {$class}::{$prop->getName()}", null, $e);
                    }
                }
                
                if (count($annotations) == 0 && count($def->getProperties()) == 0) {// A class must have atleast one XML-Definition, otherwise it would be absolute empty
                    $this->cache[$class] = false;
                } else {
                    $this->cache[$class] = $def;
                }
            } catch (MetaDataException $e) {
                throw new Exception("Failed to get MetaData for Class: $class", null, $e);
            }
        }
        return $this->cache[$class];
    }
    
    /**
     * Returns a assoc list of Annotation Objects by its Type. Every Annot-Type is only allowed once.
     * 
     * Example:<pre>
     * array(
     *   'Document' => object(\jas\xml\Meta\Document) {...},
     *   'Element' => object(\jas\xml\Meta\Element) {...},
     * )
     * </pre>
     * @param array $annotations List of Annotations retrieved by Doctrines Annotation-Reader for a Class/Property/Method
     * @throws MetaDataException When an Annotation, is defined more than once
     * @return array
     * @todo Also validate incompatible Annotations e.g. Element & Attribute
     */
    protected static function annotList($annotations) {
        $ans = array();
        foreach ($annotations as $an) {
            if ($an instanceof Annotation) {
                $t = $an->getName();
                if ($an->isSingleAnnotation()) {
                    if (isset($ans[$t]))
                        throw new MetaDataException("There can be only one {$an->getName()}-Annotation");
                    $ans[$t] = $an;
                } else {
                    if (!isset($ans[$t]))
                        $ans[$t] = array();
                    $ans[$t][] = $an;
                }
            }
        }
        return $ans;
    }
    
    protected function parseDefinition(Definition $def, array $annotations) {
        $annotations = static::annotList($annotations);
        foreach ($annotations as $an) {
            foreach ((array) $an as $t => $_an) {
                if ($def instanceof Klass)
                    $an->defineKlass($def);
                elseif ($def instanceof Property)
                    $an->defineProperty($def);
                else
                    throw new Exception("Methods aren't yet supported");
            }
        }
    }
}