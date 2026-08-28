<?php
namespace App\Src\Modules\Product;

use App\Src\Core\DB\Connection;
use App\Database\Schemas\SchemaSQL as Schema;
use App\Logs\Logger;
use App\Src\Core\HTTP\Request;
// DTOS
use App\Src\Modules\Product\DTOs\CreateProductDTO;
// Throws
use mysqli_sql_exception;

class ProductService {
    private Connection $connection;
    private Logger $log;
    private Schema $schema;

    public function __construct() {
        $this->connection = new Connection();
        $this->log = new Logger();
        $dbConnection = $this->connection->getConnection();
        // Init schema
        $this->schema = new Schema($dbConnection, $this->log);
    }

    /**
     * Service to call products list
     */
    public function productsList(Request $req): array {
        try {
            $init = microtime(true);
            $products = $this->schema->get('products', ['id','name','price','stock']);
            $end = microtime(true);

            $req->track('3-ListProductQueryTime', ($end - $init));

            return $products;
            
        } catch (mysqli_sql_exception $e) {
            $this->log->error(sprintf( "Failed to get rows on '%s': \nException: %s", 'products', $e->getMessage()));

            throw new mysqli_sql_exception( "Sorry we had an error on list", 503 );
        }
    }
    
    /**
     * Service to insert product row on schema
     */
    public function productStore(Request $req, CreateProductDTO $dto): array {
        try {
            $init = microtime(true);
            $product = $this->schema->insert('products', $dto->data());
            $end = microtime(true);

            $req->track('3-StoreProductQueryTime', ($end - $init));

            return $product;
            
        } catch (mysqli_sql_exception $e) {
            $this->log->error(sprintf( "Failed to store row on '%s': \nException: %s", 'products', $e->getMessage()));

            throw new mysqli_sql_exception( "Sorry we had an error on store", 503 );
        }
    }
}