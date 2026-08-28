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
     * Get query
     * 
     * In this method you can call db rows by table with metadata information
     * - params
     *  table <string> => Target table
     *  metadata <array> => all select properties
     *      - columns: <array> => column names such as ['name', 'age', ...] could be nullable
     */
    public function get(string $table, array $metadata): array {
        try {
            $columns = (isset($metadata['columns'])) 
                ? implode(", ", $metadata['columns'])
                : '*';

            $query = sprintf( "SELECT %s FROM %s", $columns, $table);
            
            $res = $this->connection->query($query);
            return $res->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            $this->logger->error(sprintf( "Failed to get records on table '%s': \nException: %s", $table, $e->getMessage()));

            throw $e;
        }
    }

    /**
     * Insert one record on table
     * 
     * params:
     *  table: string => table name
     *  columns: array<key,value> => Array of values by [col => value, ...]
     * return
     *  void it only do inserts on DB
     * throws
     *  mysql sql exception => do a log message and throw error
     */
    public function insert(string $table, array $columns): array {
        try {
            $cols = array_keys($columns);
            $values = array_values($columns);

            $query = sprintf("INSERT INTO %s (%s) VALUES (%s)", $table, implode(", ", $cols), implode(", ", $values));
            
            $res = $this->connection->query($query);
            $id = $this->connection->insert_id;

            $result = $this->connection->query( sprintf("SELECT * FROM %s WHERE id = %s", $table, $id));

            return $result->fetch_assoc();
        } catch (mysqli_sql_exception $exception) {
            $this->logger->error(sprintf( "Failed to insert on table '%s': \nException: %s", $table, $exception->getMessage()));

            throw $exception;
        }
    }
    
    /**
     * Insert two or more records on table
     * 
     * params:
     *  table: string => table name
     *  records: array<array<key,value>> => Array of records with values such as [[col => value, ...], [col => value, ...], ...]
     * return
     *  void it only do inserts on DB
     * throws
     *  mysql sql exception => do a log message and throw error
     */
    public function insertMultiRecords(string $table, array $records) {
        try {
            $cols = array_keys($records[0]);
            $values = array_reduce($records, function (array $carry, $record) {
                $carry[] = sprintf("(%s)", implode(", ", array_values($record)));
                return $carry;
            }, []);

            echo sprintf("INSERT INTO %s (%s) VALUES %s", $table, implode(", ", $cols), implode(", ", $values));
            $query = sprintf("INSERT INTO %s (%s) VALUES %s", $table, implode(", ", $cols), implode(", ", $values));
            $this->connection->query($query);
        } catch (mysqli_sql_exception $exception) {
            $this->logger->error(
                sprintf( "Failed to insert on table table '%s': \nException: %s", $table, $exception->getMessage())
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