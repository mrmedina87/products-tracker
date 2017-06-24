<?php 

  include_once 'resource.php';

  class Language extends Resource {
    function __construct($method, $payload) {
      parent::__construct($method, $payload);
    }

    function processRequest() {
      if($this->method == "GET") {
        $link = mysqli_connect('localhost', 'root', '', 'dury');

        mysqli_set_charset($link,'utf8');

        $result = mysqli_query($link, "select * from languages");
        echo '{"languages":[';
        for ($i = 0; $i < mysqli_num_rows($result); $i++) {
          echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
        }
        echo ']}';

        mysqli_close($link);
      }
      else {
        http_response_code(404);
      }
    }
  }
?>