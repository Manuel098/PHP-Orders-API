<?php
namespace App\Src\Modules\Product;

use App\Src\Core\HTTP\Request;
// DTOS
use App\Src\Modules\Product\DTOs\CreateProductDTO;

class ProductActions {
    private ProductService $service;

    public function __construct() {
        $this->service = new ProductService();
    }

    /**
     * Trigger action to dispatch list services
     */
    public function fetchProducts(Request $req): array { 
        $init = microtime(true);
        $products = $this->service->productsList($req);
        $end = microtime(true);

        $req->track('2-FetchAction', ($end - $init));
        return $products;
    }
    
    /**
     * Trigger action to dispatch store services
     */
    public function storeProduct(Request $req, CreateProductDTO $dto): array { 
        $init = microtime(true);
        $products = $this->service->productStore($req, $dto);
        $end = microtime(true);

        $req->track('2-StoreAction', ($end - $init));
        return $products;
    }
}