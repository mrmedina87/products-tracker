<?php 

  include_once 'resource.php';

  class Product extends Resource {
    function __construct($method, $payload) {
      parent::__construct($method, $payload);
    }

    function processRequest($request) {
      $link = mysqli_connect('localhost', 'root', '', 'dury');
      mysqli_set_charset($link,'utf8');

      switch($this->method) {
        case "GET":
          $query = "select * from products";
          if($request[1] != "") {
            $query = $query . " where products_id = " . $request[1];
          } 
          $result = mysqli_query($link, $query);
          if($result) {
            echo '{products:[';
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
              echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
            }
            echo ']}';
          }
          else {
            http_response_code(400);
          }
          break;
        case "DELETE":
          if($request[1] != "") {
            $delete = "delete from products where products_id = " . $request[1];
            $result = mysqli_query($link, $delete);
            if($result) {
              $delete = "delete from products_description where products_id = " . $request[1];
              if(mysqli_query($link, $delete)) {
                echo '{"delete": "ok"}';
              }
            }
            http_response_code(400);
          }
          else {
            http_response_code(400);
          }
          break;
        case "POST":
          if(is_array($this->payload) && array_key_exists("products_price", $this->payload) && array_key_exists("products_reference", $this->payload)) {
            $float_products_price = floatval($this->payload["products_price"]);
            $products_reference = $this->payload["products_reference"];
            if(is_float($float_products_price) && is_string($products_reference)) {
              $insert = "INSERT INTO `PRODUCTS` (`products_price`, `products_reference`) VALUES ($float_products_price, '$products_reference')";
              $result = mysqli_query($link, $insert);
              if($result) {
                echo '{"insert": "ok"}';
              }
              else {
                echo '{"errorMessage": "Something went wrong while trying to save this value. Try again or call a web admin"}';
                http_response_code(400);  
              }
            }
            else {
              echo '{"errorMessage": "Wrong values"}';
              http_response_code(400);  
            }
            
          }
          else {
            echo '{"errorMessage": "Not enough arguments"}';
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