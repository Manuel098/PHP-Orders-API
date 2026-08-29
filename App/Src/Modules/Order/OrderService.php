<?php
namespace App\Src\Modules\Order;

use App\Src\Core\DB\Connection;
use App\Database\Schemas\SchemaSQL as Schema;
use App\Logs\Logger;
use App\Src\Core\HTTP\Request;
// DTOS
use App\Src\Modules\Order\DTOs\CreateOrderDTO;
use App\Src\Modules\Order\DTOs\QueryFiltersOrdersDTO;
// Objects
use App\Src\Modules\Order\Objects\OrderList;
// Throws
use mysqli_sql_exception;

class OrderService {
    private Logger $log;
    private Schema $schema;

    public function __construct(Schema $schema) {
        $this->log = new Logger();
        $this->schema = $schema;
    }

    /**
     * Check if the products has stock
     * 
     * params:
     *  - req <Request> => Just to track the time on method
     *  - items <array[productId, quantity]> => A list of items with 2 params
     * return:
     *  - res <array[hasStock, items]> => 
     *      - hasStck <bool> Just to continue with the workflow
     *      - items <array [valid, id, price, quantity ]> => a list of items each one with a calculate final price 
     */
    public function checkItems(Request $req, array $items): array {
        try {
            $init = microtime(true);
            // Make qty map [productId => quantity]
            $quantities = array_column($items, 'quantity', 'productId');
            $cases = implode( "\n", array_map( fn($productId, $quantity) => "WHEN id = {$productId} AND stock >= {$quantity} THEN 1", array_keys($quantities), $quantities ) );

            // Call DB query
            $dbItems = $this->schema->get('products', [
                'columns' => [ "CASE {$cases} ELSE 0 END AS valid", 'id', 'price' ],
                'whereIn' => ['key' => 'id', 'value' => sprintf( "(%s)", implode(', ', array_column($items, 'productId')))]
            ]);

            // prepare Response
            $res = [ 'hasStock' => true, 'items' => [], 'total' => 0 ];

            // last foreach to validate all items has stock and calculate the final price
            foreach ($dbItems as $item) {
                $res['total'] += $item['price'] * $quantities[$item['id']];
                $res['hasStock'] = $res['hasStock'] && $item['valid'] === '1';
                $res['items'][] = [ ...$item, 'quantity' => $quantities[$item['id']] ];
            }

            return $res;

        } catch (mysqli_sql_exception $e) {
            $this->log->error(sprintf( "Failed to get rows on '%s':\nException: %s", 'orders', $e->getMessage() ));
            throw new mysqli_sql_exception( 'Database connection failed', 400 );
        } finally {
            $req->track( '3.1-CheckItemsService', microtime(true) - $init );
        }
    }

    /**
     * Create Order and push items
     * params:
     *  - req <Request> => Just to track the time on method
     *  - dto <object> => Used to build payload and get rows
     */
    public function makeOrder(Request $req, CreateOrderDTO $dto): array {
        try {
            $init = microtime(true);
            // Create Order
            $order = $this->schema->insert('orders', $dto->getPayload());
            $order['items'] = $dto->getPayloadItems($order['id']);
            // Insert Items
            $this->schema->insertMultiRecords('order_items', $order['items']);

            return $order;
        } catch (mysqli_sql_exception $e) {
            $this->log->error(sprintf( "Failed to store rows on '%s':\nException: %s", 'orders', $e->getMessage() ));
            throw new mysqli_sql_exception( 'Database connection failed', 400 );
        } finally {
            $req->track( '3.2-CreateOrderService', microtime(true) - $init );
        }
    }

    public function updateItemsStock(Request $req, array $items): void {
        try {
            $init = microtime(true);
            
            $values = array_reduce($items, function(string $carry, array $item) {
                $carry .= sprintf( " WHEN %s THEN stock - %s ", $item['productId'], $item['qty']);
                return $carry;
            }, 'stock = CASE id ');
            $values .= ' END';

            $dbItems = $this->schema->update('products', [
                'values' => $values,
                'whereIn' => ['key' => 'id', 'value' => sprintf( "(%s)", implode(', ', array_column($items, 'productId')))]
            ]);
        } catch (mysqli_sql_exception $e) {
            $this->log->error(sprintf( "Failed to update products on '%s' table:\nCode: %s \nException: %s", 'products', $e->getCode(), $e->getMessage() ));
            switch ($e->getCode()) {
                case 3819:  throw new mysqli_sql_exception( 'Sorry we are out of stock to complete the request', 400 );
                default:    throw new mysqli_sql_exception( 'Database connection failed', 400 );
            }
            
        } finally {
            $req->track( '3.1-UpdateProductsStockService', microtime(true) - $init );
        }   
    }

    public function updateOrder(Request $req, array $order, string $status): void {
        try {
            $init = microtime(true);

            $dbItems = $this->schema->update('orders', [
                'values' => sprintf('status = "%s"', $status),
                'where' => [sprintf(' id = %s ', $order['orderId'])]
            ]);
        } catch (mysqli_sql_exception $e) {
            $this->log->error(sprintf( "Failed to update order on '%s' table:\nCode: %s \nException: %s", 'products', $e->getCode(), $e->getMessage() ));
            throw new mysqli_sql_exception( 'Database connection failed', 400 );
        } finally {
            $req->track( '3.2-UpdateProductStatusService', microtime(true) - $init );
        }
    }

    public function getOrdersList(Request $req, QueryFiltersOrdersDTO $dto): OrderList {
        try {
            $wheres = [];
            $where = '';
            if (!is_null($dto->status())) {
                array_push($wheres, sprintf('status = "%s"', $dto->status()));
            }
            if (!is_null($dto->customerId())) {
                array_push($wheres, sprintf('customer_id = %s', $dto->customerId()));
            }
            if (!empty($wheres)) {
                $where = sprintf("WHERE %s", implode(" AND ", $wheres));
            }
            $init = microtime(true);
            $table = sprintf('(SELECT * FROM orders %s ORDER BY id DESC LIMIT %s OFFSET %s) o', $where, $dto->limit(), $dto->offset());

            

            $orders = $this->schema->get($table, [
                'columns' => ['o.id as order_id, o.customer_id as customer_id, o.status as status, o.total as total, oi.product_id as productId, oi.quantity as qty , oi.unit_price as price'],
                'join' => [ 'type' => 'INNER', 'table' => 'order_items oi', 'rule' => 'oi.order_id = o.id' ],
                'order' => [ 'by' => 'o.id', 'order' => 'DESC' ]
            ]);

            return new OrderList($orders);
        } catch (mysqli_sql_exception $e) {
            $this->log->error(sprintf( "Failed to get list on '%s' table:\nCode: %s \nException: %s", 'products', $e->getCode(), $e->getMessage() ));
            throw new mysqli_sql_exception( 'Error getting your data', 400 );
        } finally {
            $req->track( '3.2-UpdateProductStatusService', microtime(true) - $init );
        }
    }


}