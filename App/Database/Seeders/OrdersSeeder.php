<?php

namespace App\Database\Seeders;

use App\Database\Schemas\SchemaSQL as Schema;


class OrdersSeeder {
    public function run(Schema $schema): void
    {
        $schema->insertMultiRecords('orders', [
            ['customer_id'  => 3,  'status' => "'confirmed'", 'total' => 9999.99,  'created_at' => json_encode(date('Y-m-d H:i:s'))],
            ['customer_id'  => 10, 'status' => "'pending'",   'total' => 18998.99, 'created_at' => json_encode(date('Y-m-d H:i:s'))],
            ['customer_id'  => 4,  'status' => "'pending'",   'total' => 31398.98, 'created_at' => json_encode(date('Y-m-d H:i:s'))],
        ]);
    }

}