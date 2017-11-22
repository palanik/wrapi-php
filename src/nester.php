<?php

namespace wrapi;

class NestedDeco {
  protected $obj;
    protected $func;

  function __construct() {
    $this->obj = new \stdClass();
      $this->func = new \stdClass();
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
      if(is_callable($this->func->$name)){
          return call_user_func_array($this->func->$name, $args);
      }
      throw new \RuntimeException('No such registered method. : '. $name);
      return;
  }

    public function setMethod($name, $func){
        $this->func->$name = $func;
    }
}
