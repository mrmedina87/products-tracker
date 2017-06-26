<?php 

  include_once 'resource.php';

  class Product extends Resource {
    function __construct($method, $payload) {
      parent::__construct($method, $payload);
    }

    private function checkDescriptions($descs) {
      $pass = true;
      foreach ($descs as $desc) {
        if(is_array($desc) 
          && array_key_exists("languages_id", $desc) 
          && array_key_exists("products_description_name", $desc) 
          && array_key_exists("products_description_short_description", $desc) 
          && array_key_exists("products_description_description", $desc)) {
          $languages_id = intval($desc["languages_id"]);
          $products_description_name = $desc["products_description_name"];
          $products_description_short_description = $desc["products_description_short_description"];
          $products_description_description = $desc["products_description_description"];

          if( !(is_int($languages_id) 
            && is_string($products_description_name)
            && is_string($products_description_short_description)
            && is_string($products_description_description)) ) {
            return false;
          }
        }
        else {
          return false;
        }
      }
      return $pass;
    }

    private function sendProduct() {
      $link = mysqli_connect('localhost', 'root', '', 'dury');
      mysqli_set_charset($link,'utf8');
      $isPut = $this->method === 'PUT';
      if($isPut) {
        $validPayload = is_array($this->payload) 
        && array_key_exists("products_price", $this->payload) 
        && array_key_exists("products_reference", $this->payload)
        && array_key_exists("products_id", $this->payload);
      }
      else {
        $validPayload = is_array($this->payload) 
        && array_key_exists("products_price", $this->payload) 
        && array_key_exists("products_reference", $this->payload);
      }
      if($validPayload) {
        $float_products_price = floatval($this->payload["products_price"]);
        $products_reference = $this->payload["products_reference"];
        if($isPut) {
          $products_id = intval($this->payload["products_id"]);
          $validParams = is_float($float_products_price) && is_string($products_reference) && is_int($products_id);
        }
        else {
          $validParams = is_float($float_products_price) && is_string($products_reference);
        }
        if($validParams) {
          if($isPut) {
            $upsertProduct = "UPDATE `products` SET
            `products_price` = $float_products_price, 
            `products_reference`= '$products_reference'
            WHERE `products_id` = $products_id";
          }
          else {
            $upsertProduct = "INSERT INTO `products` (
            `products_price`, 
            `products_reference`) 
            VALUES (
            $float_products_price, 
            '$products_reference')";
          }
          
          if(array_key_exists("descriptions", $this->payload) && is_array($this->payload["descriptions"])) {
            $descs = $this->payload["descriptions"];
            if($this->checkDescriptions($descs)) {
              if(mysqli_query($link, $upsertProduct)) {
                if(!$isPut) {
                  $product_id = $link->insert_id;
                }
                $descrUpserted = true;
                foreach ($descs as $desc) {
                  $languages_id = intval($desc["languages_id"]);
                  $products_description_name = $desc["products_description_name"];
                  $products_description_short_description = $desc["products_description_short_description"];
                  $products_description_description = $desc["products_description_description"];
                  $isDescrUpdate = false;
                  if(array_key_exists("products_description_id", $desc)) {
                    $products_description_id = intval($desc["products_description_id"]);
                    $upsertDescription = "UPDATE `products_description` SET 
                    `products_id` = $products_id,
                    `languages_id` = $languages_id,
                    `products_description_name` = '$products_description_name', 
                    `products_description_short_description` = '$products_description_short_description', 
                    `products_description_description` = '$products_description_description'
                    WHERE `products_description_id` = $products_description_id";
                  }
                  else {
                    $upsertDescription = "INSERT INTO `products_description` (
                    `products_id`,
                    `languages_id`,
                    `products_description_name`, 
                    `products_description_short_description`, 
                    `products_description_description`) 
                    VALUES (
                    $products_id, 
                    $languages_id, 
                    '$products_description_name',
                    '$products_description_short_description',
                    '$products_description_description')";
                    $deleteOldDescription = "DELETE FROM `products_description` WHERE `products_id` = $products_id AND `languages_id` = $languages_id";
                    $resDelete = mysqli_query($link, $deleteOldDescription);
                    if(!$resDelete) {
                      $descrUpserted = false;
                    }
                  } 
                  
                  $result = mysqli_query($link, $upsertDescription);
                  if(!$result) {
                    $descrUpserted = false;
                  }
                }
                if($descrUpserted) {
                  if(!$isPut) {
                    echo '{"Inserted": "Product and descriptions succesfully inserted"}';  
                  }
                  else {
                    echo '{"Updated": "Product and descriptions succesfully updated"}'; 
                  }
                }
                else {
                  echo '{"errorMessage": "Something went wrong while trying to save a description, process aborted"}';
                }
              }
              else {
                echo '{"errorMessage": "Something went wrong while trying to save a product, process aborted"}';
                http_response_code(400);
              }
                
            }
            else {
              echo '{"errorMessage": "Descriptions bad format"}';
              http_response_code(400);
            }  
          }
          else {
            echo '{"errorMessage": "Not enough arguments"}';
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
      mysqli_close($link);
    }

    function processRequest($request) {
      $link = mysqli_connect('localhost', 'root', '', 'dury');
      mysqli_set_charset($link,'utf8');

      switch($this->method) {
        case "GET":
          $query = "select * from products";
          if(isset($request[2])) {
            $query = $query . " where products_id = " . $request[2];
          } 
          $result = mysqli_query($link, $query);
          if($result) {
            echo '{"products":[';
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
          if($request[2] != "") {
            $delete = "delete from products where products_id = " . $request[2];
            $result = mysqli_query($link, $delete);
            if($result) {
              $delete = "delete from products_description where products_id = " . $request[2];
              if(mysqli_query($link, $delete)) {
                echo '{"delete": "ok"}';
              }
              else {
                http_response_code(400);
              }
            }
            else {
              http_response_code(400);
            }
          }
          else {
            http_response_code(400);
          }
          break;
        case "POST":
        case "PUT":
          $this->sendProduct();
          break;
        default:
          http_response_code(404);
          break;
      }

      mysqli_close($link);
    }   
  }
?>