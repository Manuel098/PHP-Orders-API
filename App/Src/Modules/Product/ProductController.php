<?php
namespace App\Src\Modules\Product;

use App\Src\Core\HTTP\Response;
use App\Src\Core\HTTP\Request;
use App\Logs\Logger;
// DTOs
use App\Src\Modules\Product\DTOs\CreateProductDTO;
// Throws
use InvalidArgumentException;
use mysqli_sql_exception;

class ProductController {
    private ProductActions $actions;
    private Logger $log;

    public function __construct() {
        $this->actions = new ProductActions();
        $this->log = new Logger();
    }

    /**
     * List all Products
     * params:
     *  Request just to track request and timing NO DATA NEEDED
     */
    public function list(Request $req): void {
        try {
            $init = microtime(true);
            $products = $this->actions->fetchProducts($req);
            $end = microtime(true);

            $req->track('1-ListController', ($end - $init));

            $this->log->track($req);
            Response::json([ 'sussess' => true, 'data' => $products ], 200);
        } catch (mysqli_sql_exception $e) {
            $this->log->track($req);
            Response::json([ 'sussess' => false, 'message' => $e->getMessage() ], 500);
        }
    }
    
    /**
     * Store a product
     * params:
     *  req <Request> => Product payload which will be validated on DTO
     * respons:
     *  response <Json> => [sussess: [true, false], data: {...productData} || message => error message]
     */
    public function store(Request $req): void {
        try {
            $init = microtime(true);
            $dto = CreateProductDTO::fromArray( $req->body() );
            $product = $this->actions->storeProduct($req, $dto);
            $end = microtime(true);

            $req->track('1-StoreController', ($end - $init));

            $this->log->track($req);
            Response::json([ 'sussess' => true, 'data' => $product ], 201);
        } catch (mysqli_sql_exception | InvalidArgumentException $e) {
            Response::json([ 'sussess' => false, 'message' => $e->getMessage() ], $e->getCode() ?? 500);
        }
    }
}