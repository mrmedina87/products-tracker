<?php

include_once 'config.php';
include_once 'classes/language.php';
include_once 'classes/product.php';
include_once 'classes/productdescription.php';
include_once 'main.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$input = json_decode(file_get_contents('php://input'), true);

if($request[0] === "") {
  $main = new Main();
  $main->showMain();
}
else {
  if($request[0] === "api") {
    $resource = $request[1];

    switch($resource) {
      case "language":
        $lan = new Language($method, $input);
        $lan->processRequest();
        break;
      case "product":
        $prod = new Product($method, $input);
        $prod->processRequest($request);
        break;
      case "productdesc":
        $prod = new ProductDescription($method, $input);
        $prod->processRequest($request);
        break;
      default:
        echo '{"errorMessage": "not a good url"}';
        http_response_code(404);
        break;
    } 
  }
  else {
    echo '{"errorMessage": "not a good url"}';
    http_response_code(404);
  }
   
}

?>