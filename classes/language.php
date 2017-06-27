<?php 

  include_once 'resource.php';

  class Language extends Resource {
    function __construct($method, $payload) {
      parent::__construct($method, $payload);
    }

    function __destruct() {
      parent::__destruct();
    }

    function processRequest() {
      if($this->method == "GET") {
        $result = $this->link->query("select * from languages");
        echo '{"languages":[';
        for ($i = 0; $i < mysqli_num_rows($result); $i++) {
          echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
        }
        echo ']}';
      }
      else {
        http_response_code(404);
      }
    }
  }
?>