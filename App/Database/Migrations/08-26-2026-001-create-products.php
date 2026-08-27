<?php

use App\Database\Schemas\SchemaSQL as Schema;

return new class {
    public function up(Schema $schema): void {
        $schema->create('products', [
            'id'    => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'name'  => 'VARCHAR(150) NOT NULL',
            'price' => 'DECIMAL(10, 2) NOT NULL CHECK (price >= 0)',
            'stock' => 'INT DEFAULT 0 CHECK (stock >= 0)'
        ]);
    }

    public function down(Schema $schema): void {
        $schema->drop('products');
    }
};