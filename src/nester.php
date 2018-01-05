<?php

namespace wrapi;

class NestedDeco {
  protected $obj, $func;

  function __construct() {
    $this->obj = new \stdClass();
    $this->func = new \stdClass();
  }

  public function __set($name, $val) {
    if (is_callable($val)) {
      $this->func->$name = $val;
    }
    else {
      $this->obj->$name = $val;
    }
    return $this;   // for chaining
  }

  public function __get($name) {
    if (property_exists($this->obj, $name)) {
        return $this->obj->$name;
    }

    return null;
  }

  public function __call($name, $args) {
    if (property_exists($this->func, $name) && is_callable($this->func->$name)) {
        return call_user_func_array($this->func->$name, $args);
    }

    throw new \RuntimeException('No such registered method. : '. $name);
    return;
  }
}
