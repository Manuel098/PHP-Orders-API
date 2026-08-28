<?php
namespace App\Src\Modules\Order;

use App\Src\Core\HTTP\Response;
use App\Src\Core\HTTP\Request;
use App\Logs\Logger;
// DTOs
use App\Src\Modules\Order\DTOs\CreateOrderDTO;
// Throws
use InvalidArgumentException;
use mysqli_sql_exception;

class OrderController {
    private OrderActions $actions;
    private Logger $log;

    public function __construct() {
        $this->actions = new OrderActions();
        $this->log = new Logger();
    }

    /**
     * List all Products
     * params:
     *  Request just to track request and timing NO DATA NEEDED
     */
    public function store(Request $req): void {
        try {
            $init = microtime(true);
            $dto = CreateOrderDTO::fromArray( $req->body() );
            $order = $this->actions->storeOrder($req, $dto);
            $end = microtime(true);

            $req->track('1-StoreController', ($end - $init));

            $this->log->track($req);
            Response::json([ 'sussess' => true, 'data' => $order ], 201);
        } catch (InvalidArgumentException | mysqli_sql_exception $e) {
            $this->log->track($req);
            Response::json([ 'sussess' => false, 'message' => $e->getMessage() ], $e->getCode());
        }
    }

}