<?php

namespace jas\xml\Helper;
use jas\xml\Meta\Option;
use jas\xml\Definition\Klass;
use jas\xml\Definition\Definition;

final class Helper {
    private function __consruct() {
    }
    public static function clear() {
        self::$normalizer = array();
    }
    
    /**
     * @return jas\xml\Accessor\Accessor
     */
    public static function getAccessor(Definition $def, $object) {
        $class = $def->getOption(Option::ACCESSOR, '\\jas\\xml\\Accessor\\ReflectionAccessor');
        $class = self::getClassDefaultNamespace($class, '\\jas\\xml\\Accessor\\');
        return new $class($object);
    }
    
    private static $normalizer = array();
    /**
     * @return jas\xml\Normalizer\Normalizer
     */
    public static function getNormalizer(Definition $def) {
        $class = $def->getOption(Option::NORMALIZER, '\\jas\\xml\\Normalizer\\DefaultNormalizer');
        $class = self::getClassDefaultNamespace($class, '\\jas\\xml\\Normalizer\\');
        if (!isset(self::$normalizer[$class]))
            self::$normalizer[$class] = new $class();
        return self::$normalizer[$class];
    }
    
    public static function getClassDefaultNamespace($class, $defaultNS) {
        if (strpos($class, '\\') === false && class_exists($defaultNS.$class))
            $class = $defaultNS.$class;
        return $class;
    }
    
    public static function isPrimitive($data_type) {
        switch ($data_type) {
            case "boolean":
            case "boo":
            case "integer":
            case "int":
            case "double":
            case "float":
            case "string":
                return true;
            default:
                return false;
        }
    }
}