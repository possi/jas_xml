<?php

namespace jas\xml\Normalizer;

interface Normalizer {
    public function valueToString($value);
    public function stringToValue($string);
}