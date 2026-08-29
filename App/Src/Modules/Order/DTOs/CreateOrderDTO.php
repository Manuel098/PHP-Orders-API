<?php

namespace App\Src\Modules\Order\DTOs;

use InvalidArgumentException;
use App\Src\Core\DB\Connection;
use App\Database\Schemas\SchemaSQL as Schema;
use App\Logs\Logger;
// DTOS
use App\Src\Modules\Order\DTOs\CreateOrderDTO;

class CreateOrderDTO {
    public readonly int $customerId;
    public array $items;
    public float $total;

    public function __construct( int $customerId, array $items ) {
        $this->customerId = $customerId;
        $this->items      = $items;
    }

    public static function fromArray(array $data): self {
        if ( !isset($data['customerId']) || !isset($data['items']) ) {
            throw new InvalidArgumentException( 'customerId and items are required', 422 );
        }
        if ( !is_integer($data['customerId']) || $data['customerId'] < 1 ) {
            throw new InvalidArgumentException( 'customerId must be a valid id', 400 );
        }
        if ( !is_array($data['items']) || count($data['items']) < 1 ) {
            throw new InvalidArgumentException( 'items must be a valid id and can not be empty', 400 );
        }
        foreach ($data['items'] as $item) {
            if (!is_array($item) || !isset($item['productId']) || !isset($item['quantity'])) {
                throw new InvalidArgumentException( 'Each Item need to have a productId and Quantity', 422 );
            }
            if (!is_int($item['productId']) || $item['productId'] < 1) {
                throw new InvalidArgumentException( 'Each Item need to have a valid productId', 400 );
            }
            if (!is_int($item['quantity']) || $item['quantity'] < 1) {
                throw new InvalidArgumentException( 'Each Item need to have a valid numeric quantity', 400 );
            }
        }

        // Make connection to validate all items exist
        $connection = new Connection();
        $log = new Logger();
        $dbConnection = $connection->getConnection();
        // Init schema
        $schema = new Schema($dbConnection, $log);
        $countItems = $schema->get('products', [
            'columns' => ['COUNT(*) as count'],
            'whereIn' => ['key' => 'id', 'value' => sprintf( "(%s)", implode(', ', array_column($data['items'], 'productId')))]
        ]);

        if ( (int)$countItems[0]['count'] !== count($data['items']) ) {
            throw new InvalidArgumentException( 'All items must be present in the table', 404 );
        }

        return new self( customerId: $data['customerId'], items: $data['items'] );
    }

    // SETERS
    public function setTotal(float $total) { $this->total = $total; }
    public function setItems(array $items) { $this->items = $items; }
    // GETERS
    public function getItems():array { return $this->items; }
    public function getPayload(): array {
        return [
            'customer_id' => $this->customerId,
            'status'      => sprintf('"%s"', 'pending'),
            'total'       => $this->total,
            'created_at'  => sprintf('"%s"', date('Y-m-d H:i:s'))
        ];
    }
    public function getPayloadItems(int $orderId): array {
        return array_map( fn ($item) => [
            'order_id' => $orderId,
            'product_id' => $item['id'],
            'quantity' => $item['quantity'],
            'unit_price' => round($item['price'], 2),
        ], $this->items);
    }
}