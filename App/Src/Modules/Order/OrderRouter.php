<?php
use App\Src\Modules\Order\OrderController;

$orderController = new OrderController();

$router->post( '/orders', [$orderController, 'store'] );
$router->patch( '/orders/{id}/confirm', [$orderController, 'confirm']);
$router->patch( '/orders/{id}/cancel', [$orderController, 'cancel']);
