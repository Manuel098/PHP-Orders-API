<?php

namespace App\Database\Seeders;

use App\Database\Schemas\SchemaSQL as Schema;


class ProductSeeder {
    public function run(Schema $schema): void
    {
        $schema->insertMultiRecords('products', [
            [ 'name' => "'Nintendo Swich'",   'price' => 9999.99,     'stock' => 4],
            [ 'name' => "'Xbox Series X'",    'price' => 12399.99,    'stock' => 15],
            [ 'name' => "'Xbox Series S'",    'price' => 10589.49,    'stock' => 21],
            [ 'name' => "'PlayStation S'",    'price' => 8999.00,     'stock' => 1]
        ]);
    }

}