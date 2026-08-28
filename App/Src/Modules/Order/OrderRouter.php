<?php
use App\Src\Modules\Order\OrderController;

$orderController = new OrderController();

$router->post( '/orders', [$orderController, 'store'] );
