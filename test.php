<?php

require_once __DIR__ . '/autoload.php';

use App\Src\Core\HTTP\Request;
use App\Src\Modules\Product\ProductController;

// TEST INSERT PRODUCTS
try {
    $req = new Request(true);

    $req->mSet('POST');
    $req->uSet('/products');

    echo "INSERT TESTS\n\n";
    $req->bSet([ 'name' => 'Test Product', 'stock' => 123, 'price' => 123.456789 ]) ;
    $product = runTestController(fn () => (new ProductController())->store($req));
    if ($product['sussess'] === true) { echo "Test insert a valid product: PASSED\n"; }

    $req->bSet([ 'name' => 'Test Product', 'price' => 123.456789 ]);
    $product = runTestController(fn () => (new ProductController())->store($req));
    if ($product['sussess'] === false) { echo "Test insert an incomplete product: PASSED\n"; }
    
    $req->bSet([ 'name' => 'Test Product', 'stock' => -123, 'price' => 123.456789 ]);
    $product = runTestController(fn () => (new ProductController())->store($req));
    if ($product['sussess'] === false) { echo "Test insert a negative value on stock product: PASSED\n"; }

} catch (Throwable $e) {
    echo "Test cases on INSERT PRODUCT: FAILED\n";
    echo $e->getMessage() . "\n";
}

function runTestController(callable $callback) {
    ob_start();
    $callback();
    $output = ob_get_clean();

    return $result = json_decode($output, true);
}