<?php

use App\Database\Schemas\SchemaSQL as Schema;

return new class {
    public function up(Schema $schema): void {
        $schema->create('order_items', [
            'id'            => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'order_id'      => 'BIGINT UNSIGNED NOT NULL',
            'product_id'    => 'BIGINT UNSIGNED NOT NULL',
            'quantity'      => 'INT DEFAULT 0 CHECK (quantity >= 0)',
            'unit_price'    => 'NUMERIC(18, 4) DEFAULT 0.0000 CHECK (unit_price >= 0)',
            'CONSTRAINT fk_order_items_orders'      => 'FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE',
            'CONSTRAINT fk_order_items_products'    => 'FOREIGN KEY (product_id) REFERENCES products(id)'
        ]);
    }

    public function down(Schema $schema): void {
        $schema->drop('order_items');
    }
};