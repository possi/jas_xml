<?php

namespace jas\xml\Meta;

/**
 * Define a Key-Value-Map of type-name (Key) and absolute class (Value). The class should be auto-load-abler or already
 * loaded when needed.
 * 
 * Example:
 *   @Xml\TypeMap({
 *     "foo_bar" = "\MyNamespace\Foo\Bar",
 *     "hello_world" = "\bundle\World\Hello
 *   })
 * 
 * 
 * @Annotation
 * @Target({"PROPERTY"})
 */
class TypeMap extends Annotation {
    public $value = null;
}
