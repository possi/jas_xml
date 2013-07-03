<?php

namespace jas\xml\Definition;

class Generic {
    protected $options = array();
    
    public function getOption($opt, $default = null) {
        return isset($this->options[$opt]) ? $this->options[$opt] : $default;
    }
    public function getOptions() {
        return $this->options;
    }
    
    /**
     * @param array $an Assoc-List of Annotations {@see jas\xml\HelperMetaStorage::annotList}
     */
    public function parse($an) {
        if (isset($an['Option']))
            $this->options = array_merge($this->options, $an['Option']->getValues());
    }
}