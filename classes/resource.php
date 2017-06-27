<?php 
  class Resource {
    protected $method;
    protected $payload;
    protected $link;
    function __construct($method, $payload) {
      $this->method = $method;
      $this->payload = $payload;
      $this->link = new mysqli('localhost', 'root', '', 'dury');
      $this->link->set_charset('utf8');
    }

    function showProps() {
      var_dump($this);
    }

    function __destruct() {
      $this->link->close();
    }
  }
?>