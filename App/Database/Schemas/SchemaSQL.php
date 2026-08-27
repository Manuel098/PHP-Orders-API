<?php
namespace App\Database\Schemas;

use mysqli;
use mysqli_sql_exception;
use App\Logs\Logger;

class SchemaSQL
{
    private mysqli $connection;
    private Logger $logger;

    /**
     * Init connection and logger 
     */
    public function __construct(mysqli $connection, Logger $logger) {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    /**
     * Create DB table function
     * params:
     *  $table: string => table name  
     *  $columns: array< columnName: definition > => array of columns
     * return:
     *  void
     * throws:
     *  mysqli sql Exception
     */
    public function create(string $table, array $columns): void
    {
        try {
            $cols = [];

            foreach ($columns as $columnName => $definition) {
                $cols[] = (str_contains($columnName, 'CONSTRAINT'))
                    ? "{$columnName} {$definition}"
                    : "`{$columnName}` {$definition}";
            }
            $query = sprintf( "CREATE TABLE `%s` (%s)", $table, implode(",\n", $cols) );

            $this->connection->query($query);   
        } catch (mysqli_sql_exception $exception) {
            $this->logger->error(
                sprintf( "Failed to create table '%s': \nException: %s", $table, $exception->getMessage())
            );

            throw $exception;
        }
    }

    /**
     * Delete DB table function
     * params:
     *  $table: string => table name
     * return:
     *  void
     * throws:
     *  mysqli sql Exception
     */
    public function drop(string $table): void
    {
        try {
            $query = sprintf( "DROP TABLE IF EXISTS %s", $table);

            $this->connection->query($query);   
        } catch (connectionException $exception) {
            $this->logger->error(
                sprintf( "Failed to remove table '%s': \nException: %s", $table, $exception->getMessage())
            );

            throw $exception;
        }
    }
}