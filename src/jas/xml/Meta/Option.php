<?php

namespace jas\xml\Meta;

/**
 * Example-Usages:
 *  - @Xml\Option({"formatOutput" = "true"})
 *  - @Xml\Option({"formatOutput" = "false", "preserveWhiteSpace" = "no"})
 * Known-Options:
 *  - formatOutput (bool): {@see \DOMDocument::formatOutput} (only on Classes with @Xml\Document)
 *  - preserveWhiteSpace (bool): {@see \DOMDocument::formatOutput} (only on Classes with @Xml\Document)
 *  - accessor (className): The class to access the Object-Properties. Default: {@see jas\xml\Accessor\ReflectionAccessor}
 *  - normalizer (className): The class to convert values to strings, and back. Default: {@see jax\xml\Normalizer\DefaultNormalizer}
 *  
 * Value-Formats:
 *  - bool: Everything not empty except "false", "no" or "off" is interpreted as true.
 * 
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class Option extends Annotation {
    const FORMAT_OUTPUT = 'formatOutput';
    const PRESERVE_WHITE_SPACE = 'preserveWhiteSpace';
    const ACCESSOR = 'accessor';
    const NORMALIZER = 'normalizer';
    
    public $value;
    
    public function getValues() {
        $return = $this->value;
        foreach ($return as $name => &$value) {
            if ($name == 'formatOutput' || $name == 'preserveWhiteSpace') {
                $value = self::bool($value);
            }
        }
        return $return;
    }
    private static function bool($value) {
        return !empty($value) && $value != "false" && $value != "no" && $value != "off";
    }
}