<?php

namespace wrapi;

class NestedDeco {
  protected $obj;

  function __construct() {
    $this->obj = new \stdClass();
  }

  public function __set($name, $val) {
    $this->obj->$name = $val;
    return $this;   // for chaining
  }

  public function __get($name) {
    if (property_exists($this->obj, $name)) {
        return $this->obj->$name;
    }

    return null;
  }
  
  public function __call($name, $args) {
    if (property_exists($this->obj, $name) && is_callable($this->obj->$name)) {
        return call_user_func_array($this->obj->$name, $args);
    }

    throw new \RuntimeException('No such registered method. : '. $name);
    return;
  }
}