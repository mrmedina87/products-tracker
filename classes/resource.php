<?php 
  class Resource {
    protected $method;
    protected $payload;
    function __construct($method, $payload) {
      $this->method = $method;
      $this->payload = $payload;
    }

    function showProps() {
      var_dump($this);
    }
  }
?>