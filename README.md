jas XML-Package
=========

A XML (Un-)Serialization Library, full configureable via annotations.

Installation:
-------------
Add this line to your composer.json "require" section:

### composer.json
```json
    "require": {
       ...
       "jas/xml": "*"
```

Usage
-----

```php
use jas\xml\Meta as Xml;
/**
 * @Xml\Document(rootNode = "foo")
 */
class MyObject {
    /**
     * @var int
     * @Xml\Attribute(name="bar")
     */
    protected $foo = 4;
    /**
     * @var string
     * @Xml\Element(name="hello")
     */
    protected $bar = "hello world";
}

$xml = jas\xml\Writer::toXML(new MyObject); -> /*
<?xml version="1.0" encoding="utf-8"?>
<foo bar="4">
  <hello>world</hello>
</foo>
*/

$myobject = jas\xml\Reader::fromXML('MyObject', $xml);
```

TODO
----
 - Outsource Reader/Writer-Logic to "layouts". A Layout should describe, how data is mapped between object and XML. This is
    especially for Lists which might be mixed up by different types of elements, ... That will be a very complex step.
 - Group Writer an Reader into a "Processor" that can be configuried with default options, or at which XML-Definitions
    can be set without Annotations (to support php or yaml configurations and so on).
    (implementation issue: how to manage "static" usage via reader/writer!?)
 - Read attribute/element type from @var-doccomment with namespace parsing
 - Namespace support
 - Dynamic TypeMap to automaticly convert type-name to class name in an php namespace via callback