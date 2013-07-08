<?php

namespace jas\xml\Definition;
use jas\xml\Helper\Helper;
use jas\xml\Exception;

/**
 * @property bool $formatOutput {@see \DOMDocument::formatOutput} (@Xml\Document only)
 * @property bool $preserveWhiteSpace {@see \DOMDocument::preserveWhiteSpace} (@Xml\Document only)
 * @property string $normalizer Class-Name of the used {@see \jas\xml\Normalizer\Normalizer}, defaults to
 *     {@see \jas\xml\Normalizer\DefaultNormalizer} 
 * @property string $accessor Class-Name of the used {@see \jas\xml\Accessor\Accessor}, defaults to
 *     {@see \jas\xml\Accessor\ReflectionAccessor}
 * @property string $classAttributeName The name of the Attribute in which the class of an element is stored, when the
 *     type isn't defined. Defaults to "class"
 * @property string $typeAttributeName The name of the Attribut in which the type-name ({@see \jas\xml\Meta\TypeMap}) of
 *     the class of the element is stored, wehn the type isn't defined. Defaults to "type".
 */
final class Options implements \Serializable {
    private $_options = array();
    public function __get($key) {
        switch ($key) {
            case "formatOutput":
            case "preserveWhiteSpace":
            case "normalizer":
            case "accessor":
            case "classAttributeName":
            case "typeAttributeName":
                return isset($this->_options[$key]) ? $this->_options[$key]: null;
            default:
                throw new Exception("Unknown Option {$key}");
        }
    }
    public function __set($key, $val) {
        switch ($key) {
            case "formatOutput":
            case "preserveWhiteSpace":
                $this->_options[$key] = (bool) $val;
                break;
            case "normalizer":
            case "accessor":
                /*$class = Helper::getClassDefaultNamespace($val, $key == 'normalizer' ? '\\jas\\xml\Normalizer\\' : '\\jas\\xml\\Accessor\\');
                if (!class_exists($class))
                    throw new Exception("Class $class not found");*/
                $this->_options[$key] = $val;
                break;
            case "classAttributeName":
            case "typeAttributeName":
                $this->_options[$key] = $val;
                break;
            default:
                throw new Exception("Unknown Option {$key}");
        }
    }
    
    public function serialize() {
        return serialize($this->_options);
    }
    public function unserialize($str) {
        $this->_options = unserialize($str);
    }
}