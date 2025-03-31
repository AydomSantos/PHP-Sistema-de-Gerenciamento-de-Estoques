<?php

include 'db.php';
include 'controller/ProductController.php';
include 'model/Product.php';

$productController = new ProductController($conn);
$products = $productController->getAllProducts();

?>

