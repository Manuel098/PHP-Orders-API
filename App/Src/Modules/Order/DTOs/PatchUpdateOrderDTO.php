<?php

namespace App\Src\Modules\Order\DTOs;

use InvalidArgumentException;
use App\Src\Core\DB\Connection;
use App\Database\Schemas\SchemaSQL as Schema;
use App\Logs\Logger;
// DTOS
use App\Src\Modules\Order\DTOs\CreateOrderDTO;

class PatchUpdateOrderDTO {
    public readonly int $orderId;
    private readonly array $order;
    
    public function __construct( int $orderId, array $order ) {
        $this->orderId = $orderId;
        $this->order = $this->formatOrder($order);
    }

    public static function fromId($id, bool $willUpdate = true): self {
        if ( !is_integer($id) || $id < 1 ) {
            throw new InvalidArgumentException( 'orderId must be a valid id', 400 );
        }

        // Make connection to validate all items exist
        $connection = new Connection();
        $log = new Logger();
        $dbConnection = $connection->getConnection();
        // Init schema
        $schema = new Schema($dbConnection, $log);

        $order = $schema->get('orders o', [
            'columns' => ['o.id as order_id, o.status as status, o.total as total, oi.product_id as productId, oi.quantity as qty , oi.unit_price as price'],
            'where' => [sprintf( " o.id = %s ", $id)],
            'join' => [ 'type' => 'INNER', 'table' => 'order_items oi', 'rule' => 'oi.order_id = o.id' ] 
        ]);

        if ( count($order) < 1 ) {
            throw new InvalidArgumentException( 'Order not found', 404 );
        }
        if ($willUpdate && $order[0]['status'] !== 'pending') {
            throw new InvalidArgumentException( 'Order must be pending', 400 );
        }

        return new self( orderId: $id, order: $order );
    }

    // GETTERS
    public function getOrder(): array { return $this->order; }

    /**
     * Format order items in an array more usable
     * params:
     *  - items <array> => all orderItem information [[...], [...], ...]
     * return:
     *  - order <array> => transformed array ['orderId' <integer>, 'status' <string>, 'total' <float>, 'items' <array>]
     */
    private function formatOrder(array $items): array {
        return array_reduce($items, function (array $carry, array $item) use ($items) {
            $carry['items'][] = [
                'productId' => (int)$item['productId'],
                'qty' => (int)$item['qty'],
                'price' => round($item['price'], 2)
            ];
            return $carry;
        }, [
            'orderId' => (int)$items[0]['order_id'],
            'status' => $items[0]['status'],
            'total' => round($items[0]['total'], 2),
            'items' => []
        ]);
    }
}