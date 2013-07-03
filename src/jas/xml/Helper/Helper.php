<?php

namespace jas\xml\Helper;
use jas\xml\Meta\Option;
use jas\xml\Definition\Klass;

final class Helper {
    private function __consruct() {
    }
    public static function clear() {
        self::$normalizer = array();
    }
    
    /**
     * @return jas\xml\Accessor\Accessor
     */
    public static function getAccessor(Klass $klass, $object) {
        $class = $klass->getOption(Option::ACCESSOR, '\\jas\\xml\\Accessor\\ReflectionAccessor');
        if (strpos($class, '\\') === false && class_exists('\\jas\\xml\\Accessor\\'.$class))
            $class = '\\jas\\xml\\Accessor\\'.$class;
        return new $class($object);
    }
    
    private static $normalizer = array();
    /**
     * @return jas\xml\Normalizer\Normalizer
     */
    public static function getNormalizer(Klass $klass) {
        $class = $klass->getOption(Option::NORMALIZER, '\\jas\\xml\\Normalizer\\DefaultNormalizer');
        if (strpos($class, '\\') === false && class_exists('\\jas\\xml\\Normalizer\\'.$class))
            $class = '\\jas\\xml\\Normalizer\\'.$class;
        if (!isset(self::$normalizer[$class]))
            self::$normalizer[$class] = new $class();
        return self::$normalizer[$class];
    }
}