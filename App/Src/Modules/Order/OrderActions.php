<?php
namespace App\Src\Modules\Order;

use App\Src\Core\DB\Connection;
use App\Database\Schemas\SchemaSQL as Schema;
use App\Logs\Logger;
use App\Src\Core\HTTP\Request;
// DTOS
use App\Src\Modules\Order\DTOs\CreateOrderDTO;
use App\Src\Modules\Order\DTOs\PatchUpdateOrderDTO;
// Throws
use mysqli_sql_exception;

class OrderActions {
    private OrderService $service;
    private Connection $connection;
    private Logger $log;
    private Schema $schema;

    public function __construct() {
        $this->connection = new Connection();
        $this->log = new Logger();
        $dbConnection = $this->connection->getConnection();
        // Init schema
        $this->schema = new Schema($dbConnection, $this->log);
        $this->service = new OrderService($this->schema);
    }

    /**
     * Create Order ACTION
     * 
     */
    public function storeOrder(Request $req, CreateOrderDTO $dto): array {
        try {
            $init = microtime(true);
            $this->schema->beginTransaction();
            ['hasStock' => $hasStock, 'items' => $items, 'total' => $total] = $this->service->checkItems($req, $dto->getItems());
            if (!$hasStock) {
                throw new mysqli_sql_exception('Temporally out of stock.', 409);
            }
            $dto->setTotal($total);
            $dto->setItems($items);

            $order = $this->service->makeOrder($req, $dto);
            $this->schema->commit();

            return $order;
        } catch (mysqli_sql_exception $e) {
            $this->schema->rollback();
            throw $e;
        } finally {
            $req->track('2-StoreAction', (microtime(true) - $init));
        }
    }

    /**
     * Update Order ACTION
     * 
     */
    public function updateOrder(Request $req, PatchUpdateOrderDTO $dto): void {
        try {
            $init = microtime(true);
            $order = $dto->getOrder();
            $this->schema->beginTransaction();
            $this->service->updateItemsStock($req, $order['items']);
            $this->service->updateOrder($req, $order, 'confirmed');
            
            $this->schema->commit();
        } catch (mysqli_sql_exception $e) {
            $this->schema->rollback();
            throw $e;
        } finally {
            $req->track('2-updateOrderAction', (microtime(true) - $init));
        }
    }

    /**
     * Cancel Order ACTION
     * 
     */
    public function cancelOrder(Request $req, PatchUpdateOrderDTO $dto): void {
        try {
            $init = microtime(true);
            $order = $dto->getOrder();
            $this->service->updateOrder($req, $order, 'cancelled');
            
        } catch (mysqli_sql_exception $e) {
            throw $e;
        } finally {
            $req->track('2-updateOrderAction', (microtime(true) - $init));
        }
    }
    
    /**
     * Get Order ACTION
     * 
     */
    public function getOrder(Request $req, PatchUpdateOrderDTO $dto): array {
        try {
            $init = microtime(true);
            return $dto->getOrder();    
        } catch (mysqli_sql_exception $e) {
            throw $e;
        } finally {
            $req->track('2-updateOrderAction', (microtime(true) - $init));
        }
    }
    
}
