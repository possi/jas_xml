<?php

namespace jas\xml\Meta;
use jas\xml\MetaDataException;
use jas\xml\Definition\Klass\Document as KlassDocument;
use jas\xml\Definition\Klass;
use jas\xml\Definition\Property;
use jas\xml\Definition\Definition;

/**
 * Example-Usages:
 *  - @Xml\Option({"formatOutput" = "true"})
 *  - @Xml\Option({"formatOutput" = "false", "preserveWhiteSpace" = "no", "accessor" = "jas\xml\Accessor\GetSetMethod"})
 *  
 * For list of all Options see {@see \jas\xml\Definition\Options}.
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
    
    public $value = array();
    
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
    protected function defineDef(Definition $def) {
        foreach ($this->value as $name => &$value) {
            if ($name == 'formatOutput' || $name == 'preserveWhiteSpace') {
                if (!($def instanceof Klass) || !($def->getTypeDefinition() instanceof KlassDocument))
                    throw new MetaDataException("Option {$name} can be only set on @Xml\\Document-Classes");
                $def->getOptions()->$name = self::bool($value);
            } else {
                $def->getOptions()->$name = $value;
            }
        }
    }
    public function defineKlass(Klass $klass) {
        $this->defineDef($klass);
    }
    public function defineProperty(Property $property) {
        $this->defineDef($property);
    }
}