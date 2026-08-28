<?php
namespace App\Src\Modules\Order;

use App\Src\Core\DB\Connection;
use App\Database\Schemas\SchemaSQL as Schema;
use App\Logs\Logger;
use App\Src\Core\HTTP\Request;
// DTOS
use App\Src\Modules\Order\DTOs\CreateOrderDTO;
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
}
