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

$myobject = jas\xml\Reader::fromXML($xml);
```