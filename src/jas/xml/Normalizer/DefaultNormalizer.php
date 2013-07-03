<?php

namespace jas\xml\Normalizer;

class DefaultNormalizer implements Normalizer {
    public function valueToString($value) {
        switch (gettype($value)) {
            case 'bool':
                return $value ? 'true' : 'false';
            default:
                return (string) $value;
        }
    }
    public function stringToValue($string) {
        // TODO: Auto-generated method stub
        
    }
    
}
