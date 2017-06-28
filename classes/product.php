<?php 

  include_once 'resource.php';

  class Product extends Resource {
    private $default_language_id = 1;
    function __construct($method, $payload) {
      parent::__construct($method, $payload);
    }

    function __destruct() {
      parent::__destruct();
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
            `products_price` = ?, 
            `products_reference`= ?
            WHERE `products_id` = ?";
            $stmt = $this->link->prepare($upsertProduct);
            $stmt->bind_param("dsi", $float_products_price, $products_reference, $products_id);
          }
          else {
            $upsertProduct = "INSERT INTO `products` (
            `products_price`, 
            `products_reference`) 
            VALUES (?,?)";
            $stmt = $this->link->prepare($upsertProduct);
            $stmt->bind_param("ds", $float_products_price, $products_reference);
          }
          
          if(array_key_exists("descriptions", $this->payload) && is_array($this->payload["descriptions"])) {
            $descs = $this->payload["descriptions"];
            if($this->checkDescriptions($descs)) {
              if($stmt->execute()) {
                if(!$isPut) {
                  $products_id = $this->link->insert_id;
                }
                $descrUpserted = true;
                foreach ($descs as $desc) {
                  $languages_id = intval($desc["languages_id"]);
                  $products_description_name = $desc["products_description_name"];
                  $products_description_short_description = $desc["products_description_short_description"];
                  $products_description_description = $desc["products_description_description"];
                  if(array_key_exists("products_description_id", $desc)) {
                    $products_description_id = intval($desc["products_description_id"]);
                    $upsertDescription = "UPDATE `products_description` SET 
                      `products_id` = ?,
                      `languages_id` = ?,
                      `products_description_name` = ?, 
                      `products_description_short_description` = ?, 
                      `products_description_description` = ?
                      WHERE `products_description_id` = ?";
                    $stmt = $this->link->prepare($upsertDescription);
                    if(!$stmt->bind_param("iisssi", 
                      $products_id,
                      $languages_id,
                      $products_description_name,
                      $products_description_short_description,
                      $products_description_description,
                      $products_description_id
                    ) || !$stmt->execute()) {
                      $descrUpserted = false;
                    }
                  }
                  else {
                    $upsertDescription = "INSERT INTO `products_description` (
                    `products_id`,
                    `languages_id`,
                    `products_description_name`, 
                    `products_description_short_description`, 
                    `products_description_description`) 
                    VALUES (?, ?, ?,?,?)";                    
                    $stmt = $this->link->prepare($upsertDescription);
                    if(!$stmt->bind_param("iisss", 
                      $products_id,
                      $languages_id,
                      $products_description_name,
                      $products_description_short_description,
                      $products_description_description
                    ) || !$stmt->execute()) {
                      $descrUpserted = false;
                    }
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
    }

    function processRequest($request) {
      switch($this->method) {
        case "GET":
          if(isset($request[2])) {
            $query = "select * from products where products_id = ?";
            $stmt = $this->link->prepare($query);
            $stmt->bind_param("i", $request[2]);
            $stmt->execute();
            $result = $stmt->get_result();
          } 
          else {
            $query = "SELECT products.products_id, 
                            products.products_reference, 
                            products.products_price,
                            products_description.products_description_name
                      FROM products
                      INNER JOIN products_description 
                      ON products.products_id=products_description.products_id 
                      WHERE products_description.languages_id = ? ORDER BY products.products_id ASC";
            $stmt = $this->link->prepare($query);
            $stmt->bind_param("i", $this->default_language_id);
            $stmt->execute();
            $result = $stmt->get_result();
          }
          if($result) {
            echo '{"products":[';
            for ($i = 0; $i < $result->num_rows; $i++) {
              echo ($i>0?',':'').json_encode($result->fetch_object());
            }
            echo ']}';
          }
          else {
            http_response_code(400);
          }
          break;
        case "DELETE":
          if($request[2] != "") {
            $delete = "delete from products where products_id = ?";
            $stmt = $this->link->prepare($delete);
            if($stmt->bind_param("i", $request[2]) && $stmt->execute()) {
              $delete = "delete from products_description where products_id = ?";
              $stmt = $this->link->prepare($delete);
              if($stmt->bind_param("i", $request[2]) && $stmt->execute()) {
                echo '{"deleted": "true"}';
              }
              else{
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

    }   
  }
?>