<?php

require_once __DIR__ . '/autoload.php';

use App\Database\Runner;
use App\Database\Schemas\SchemaSQL as Schema;
use App\Logs\Logger;
use App\Src\Core\DB\Connection;

$connection = new Connection();
$dbConnection = $connection->getConnection();
$logger = new Logger();
$schema = new Schema($dbConnection, $logger);
$runner = new Runner($schema);

$runner->rollback();
