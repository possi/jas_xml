<?php

namespace jas\xml\Definition;

abstract class Definition implements \Serializable {
    private $o;
    public function __construct() {
        $this->o = new Options();
    }
    
    public function getOption($opt, $default = null, $recursive = true) {
        if ($this->o->$opt)
            return $this->o->$opt;
        elseif ($recursive && $this->getParent())
            return $this->getParent()->getOption($opt, $default, $recursive);
        else
            return $default;
    }
    public function getOptions() {
        return $this->o;
    }
    /**
     * @return Definition
     */
    public function getParent() {
        return null;
    }
    
    public function serialize($to_string = true) {
        $s = get_object_vars($this);
        return $to_string ? serialize($s) : $s;
    }
    public function unserialize($str) {
        $d = is_string($str) ? unserialize($str) : $str;
        foreach (get_class_vars(__CLASS__) as $key) {
            if (array_key_exists($d[$key]))
                $this->{$key} = $d[$key];
        }
    }
}