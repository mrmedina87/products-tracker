<?php

include_once 'config.php';
include_once 'classes/language.php';
include_once 'classes/product.php';
include_once 'classes/productdescription.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$input = json_decode(file_get_contents('php://input'), true);
$resource = $request[0];

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
  case "":
    echo "home";
    break;
  default:
    echo "not a good url";
    break;
}
?>