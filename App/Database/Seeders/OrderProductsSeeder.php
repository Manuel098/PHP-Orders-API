<?php

namespace App\Database\Seeders;

use App\Database\Schemas\SchemaSQL as Schema;


class OrderProductsSeeder {
    public function run(Schema $schema): void
    {
        $schema->insertMultiRecords('order_items', [
            ['order_id' => 1, 'product_id' => 1, 'quantity' => 1, 'unit_price' => 9999.99],
            ['order_id' => 2, 'product_id' => 1, 'quantity' => 1, 'unit_price' => 9999.99],
            ['order_id' => 2, 'product_id' => 4, 'quantity' => 1, 'unit_price' => 8999.00],
            ['order_id' => 3, 'product_id' => 1, 'quantity' => 1, 'unit_price' => 9999.99],
            ['order_id' => 3, 'product_id' => 4, 'quantity' => 1, 'unit_price' => 8999.00],
            ['order_id' => 3, 'product_id' => 2, 'quantity' => 1, 'unit_price' => 12399.99],
        ]);
    }

}