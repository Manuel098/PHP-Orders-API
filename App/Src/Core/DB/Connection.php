<?php

namespace App\Src\Core\DB;

use mysqli;
use mysqli_sql_exception;

use App\Logs\Logger;

class Connection
{
    private mysqli $connection;

    /**
     * Do connection with mysqli 
     * Get enviroment variables to connect with DB
     * throws:
     *  mysqli sql exception and write on log file
     */
    public function __construct()
    {
        $host = getenv('DB_HOST');
        $port = (int) getenv('DB_PORT');
        $database = getenv('DB_DATABASE');
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');


        try {
            \mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            $this->connection = new mysqli(
                $host,
                $username,
                $password,
                $database,
                $port
            );

            $this->connection->set_charset('utf8mb4');

        } catch (mysqli_sql_exception $e) {
            $logger = new Logger();
            $logger->error( sprintf( "Failed to do DB connection: %s", $e->getMessage()) );

            throw new mysqli_sql_exception( 'Database connection failed: ' . $e->getMessage(), (int) $e->getCode() );
        }
    }

    /**
     * Get Mysql Connection
     * 
     * Return MySQL connection
     */
    public function getConnection(): mysqli
    {
        return $this->connection;
    }
}