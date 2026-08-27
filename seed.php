<?php

require_once __DIR__ . '/autoload.php';

use App\Database\Seeders\DatabaseSeeder;
use App\Logs\Logger;

$maxTrys = 15;
$try = 0;
$logger = new Logger();

while ($try < $maxTrys) {
    try {
        $runner = new DatabaseSeeder();
        $runner->run();
        break; 
    } catch (mysqli_sql_exception $e) {
        $try++;
        echo "Waiting DB connection... (Try $try of $maxTrys)\n";
        if (!str_contains($e->getMessage(), 'Connection refused')) {
            $try = $maxTrys+1; 
        }
        
        if ($try >= $maxTrys) {
            $logger->error(sprintf( "Failed to connect DB: \nException: %s", $e->getMessage()));
        }
        
        sleep(5);
    }
}
