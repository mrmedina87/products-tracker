<?php 

  include_once 'resource.php';

  class ProductDescription extends Resource {
    function __construct($method, $payload) {
      parent::__construct($method, $payload);
    }
    
    function __destruct() {
      parent::__destruct();
    }

    function processRequest($request) {
      switch ($this->method) {
        case 'GET':
          $query = "select * from products_description";
          if(isset($request[2])) {
            $query = $query . " where products_id = ?";
            $stmt = $this->link->prepare($query);
            $stmt->bind_param("i", $request[2]);
            $stmt->execute();
            $result = $stmt->get_result();
          } 
          else {
            $result = $this->link->query($query);
          }
          if($result) {
            echo '{"products_description":[';
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
    }
  }
?>