<?php

namespace jas\xml\Normalizer;

class Serialization extends DefaultNormalizer {
    public function valueToString($value) {
        if ((is_object($value) && !method_exists($value, '__toString')) || is_array($value))
            return serialize($value);
        else
            return parent::valueToString($value);
    }
}
