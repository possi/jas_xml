<?php

namespace jas\xml\Helper;
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
    const META_NS = 'jas\\xml\\Meta\\';
    
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
            $this->reader = new AnnotationReader();
        }
        return $this->reader;
    }
    private function a() {
        return $this->getAnnotationReader();
    }
    
    private $cache = array();
    
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
                $t = substr($c = get_class($an), strrpos($c, '\\') + 1);
                if (isset($ans[$t]))
                    throw new MetaDataException("There can be only one $t-Annotation");
                $ans[$t] = $an;
            }
        }
        return $ans;
    }
    
    public function getMeta($object) {
        $class = get_class($object);
        if (!isset($this->cache[$class])) {
            $rclass = new \ReflectionObject($object);
            try {
                $annotations = self::annotList($t=$this->a()->getClassAnnotations($rclass));
                if (count($annotations) == 0) {
                    $this->cache[$class] = false;
                } else {
                    $def = new Klass($class);
                    $def->parse($annotations);
                    
                    foreach ($rclass->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED) as $prop) {
                        /* @var $prop \ReflectionProperty */
                        if ($prop->isStatic())
                            continue;
                        try {
                            $property_annotations = self::annotList($this->a()->getPropertyAnnotations($prop));
                            if (count($property_annotations) == 0)
                                continue;
                            $pdef = new Property($def, $prop->getName());
                            $pdef->parse($property_annotations);
                            $def->addProperty($pdef);
                        } catch (MetaDataException $e) {
                            throw new Exception("Failed to get MetaData for Class-Property: {$class}::{$prop->getName()}", null, $e);
                        }
                    }
                    
                    $this->cache[$class] = $def;
                }
            } catch (MetaDataException $e) {
                throw new Exception("Failed to get MetaData for Class: $class", null, $e);
            }
        }
        return $this->cache[$class];
    }
    
    /*private function _meta($r = null) {
        $class = get_class($this);
        //$meta = Util::isDebug() ? $this->debug : Cache::get("de.jas.xml[$class]._meta");
        $meta = null;
        if (is_null($meta)) {
            $meta = array('a' => array(), 'e' => array(), 'd' => null, 'v' => null);
            $rclass = new ReflectionObject($this);
            // Attribute & Elements
            Log::debug(__CLASS__, "Retrieving element-definitions for class {$class}");
    
            foreach ($rclass->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED) as $prop) {
                if ($prop->isStatic())
                    continue;
                $el = array(); $at = array();
                $annotation = self::annot()->getPropertyAnnotations($prop);
                foreach ($annotation as $an) {
                    if ($an instanceof Meta\XmlAttribute) {
                        $at = $an->toDefinition();
                        if (empty($at['name']))
                            $at['name'] = $prop->getName();
                        if (empty($at['type'])) {
                            $doc = $prop->getDocComment();
                            if ($doc && preg_match("/^\s*\*\s+@var\s+(.*?)\s*$/m", $doc, $match))
                                $at['type'] = trim($match[1]);
                        }
                    } elseif ($an instanceof Meta\XmlList) {
                        $el = array_merge($el, $an->toDefinition());
                        if (empty($el['List*type'])) {
                            $doc = $prop->getDocComment();
                            if ($doc && preg_match("/^\s*\*\s+@var\s+(?:array|List)<(.*?)>\s*$/m", $doc, $match))
                                $el['List*type'] = trim($match[1]);
                        }
                    } elseif ($an instanceof Meta\XmlElement) {
                        if (!empty($meta['v']))
                            throw new Exception('There can be only either one Value-Attribute or Child-Elements');
                        $el = array_merge($el, $an->toDefinition());
                        if (empty($el['name']))
                            $el['name'] = $prop->getName();
                        if (empty($el['type'])) {
                            $doc = $prop->getDocComment();
                            if ($doc && preg_match("/^\s*\*\s+@var\s+(.*?)\s*$/m", $doc, $match))
                                $el['type'] = trim($match[1]);
                        }
                    } elseif ($an instanceof Meta\XmlValue) {
                        if (!empty($meta['v']) || count($meta['e']))
                            throw new Exception('There can be only either one Value-Attribute or Child-Elements');
                        $meta['v'] = $an->toDefinition();
                        $meta['v']['attr'] = $prop->getName();
                    }
                }
    
                if (!empty($el) && !empty($at)) {
                    throw new Exception('A value can only be an Attribute or an Element, not both: '.$prop->getName());
                } elseif (!empty($el)) {
                    if (!isset($el['name']))
                        throw new Exception('Missing modeName, may be you forgot @XmlElement');
                    $meta['e'][$prop->getName()] = $el;
                } elseif (!empty($at)) {
                    $meta['a'][$prop->getName()] = $at;
                }
                unset($el, $at);
            }
            // Document
            $annotation = self::annot()->getClassAnnotations($rclass);
            foreach ($annotation as $an) {
                if ($an instanceof Meta\XmlDocument)
                    $meta['d'] = $an->toDefinition();
            }
            /*if (Util::isDebug())
                $this->debug = $meta;
            else
                Cache::set("de.jas.xml[$class]._meta", $meta);* /
        }
        return isset($r) ? $meta[$r] : $meta;
    }*/
}