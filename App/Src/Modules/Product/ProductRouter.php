<?php
use App\Src\Modules\Product\ProductController;

$productController = new ProductController();

$router->get( '/products', [$productController, 'list'] );
$router->post( '/products', [$productController, 'store'] );
