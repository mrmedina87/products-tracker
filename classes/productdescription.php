<?php 

  include_once 'resource.php';

  class ProductDescription extends Resource {
    function __construct($method, $payload) {
      parent::__construct($method, $payload);
    }
    
    function processRequest($request) {
      $link = mysqli_connect('localhost', 'root', '', 'dury');
      mysqli_set_charset($link,'utf8');

      switch ($this->method) {
        case 'GET':
          $query = "select * from products_description";
          if($request[1] != "") {
            $query = $query . " where products_description_id = " . $request[1];
          } 
          $result = mysqli_query($link, $query);
          if($result) {
            echo '{products_description:[';
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
              echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
            }
            echo ']}';
          }
          else {
            http_response_code(400);  
          }
          break;
        default:
          http_response_code(404);
          break;
      }
      mysqli_close($link);
    }
  }
?>