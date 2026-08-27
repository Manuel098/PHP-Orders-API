<?php

namespace App\Database\Seeders;

use App\Database\Schemas\SchemaSQL as Schema;
use App\Src\Core\DB\Connection;
use App\Logs\Logger;

class DatabaseSeeder
{
    private Logger $logger;
    private Connection $connection;
    private Schema $schema;
    
    /**
     * Mount Seeder
     * 
     * Set Logger, Connection and Schema variables
     */
    public function __construct() {
        $this->logger = new Logger();
        $this->connection = new Connection();
        $dbConnection = $this->connection->getConnection();
        $this->schema = new Schema($dbConnection, $this->logger);
    }

    /**
     * Register and run all Seeders methods
     */
    public function run(): void {
        try {
            (new ProductSeeder())->run($this->schema);
            (new OrdersSeeder())->run($this->schema);
            (new OrderProductsSeeder())->run($this->schema);
        } catch (mysqli_sql_exception $exception) {
            $this->logger->error(sprintf( "Failed to run Seeders \nException: %s", $exception->getMessage()));

            throw $exception;
        }
        
    }
}