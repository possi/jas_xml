<?php

namespace jas\xml\Helper;
use Doctrine\Common\Annotations\PhpParser;
use jas\xml\MetaDataException;

final class Annotations {
    private function __construct() {}
    private static function classUses(ReflectionClass $class) {
        static $uses = array();
        static $parser = null;
        if (!isset($parser))
            $parser = new PhpParser;
        if (!isset($uses[$class->getName()])) {
            $uses[$class->getName()] = $parser->parseClass($class);
        }
        return $uses[$class->getName()];
    }
    private static function findClass($class, ReflectionClass $relatedclass) {
        $uses = self::classUses($relatedclass);
    
        if (isset($uses[strtolower($class)])) {
            return $uses[strtolower($class)];
        }
        if (strpos($class, '\\') !== 0) {
            $nsclass = $relatedclass->getNamespaceName() . '\\' . $class;
        } else {
            $nsclass = $class;
        }
        if (class_exists($nsclass)) {
            return $nsclass;
        } else {
            throw new MetaDataException('Class couldn\'t be found: '.$class);
        }
    }
}