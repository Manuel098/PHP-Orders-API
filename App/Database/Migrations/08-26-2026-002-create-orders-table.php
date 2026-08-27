<?php

use App\Database\Schemas\SchemaSQL as Schema;

return new class {
    public function up(Schema $schema): void {
        $schema->create('orders', [
            'id'            => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'customer_id'   => 'INT UNSIGNED NOT NULL',
            'status'        => "ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending'",
            'total'         => 'DECIMAL(11, 2) NOT NULL',
            'created_at'    => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ]);
    }

    public function down(Schema $schema): void {
        $schema->drop('orders');
    }
};