<?php
require_once __DIR__ . '/../autoload.php';

use App\Src\Core\HTTP\Request;
use App\Src\Core\Router\CRUD;

$req = new Request();
$router = new CRUD();

require_once __DIR__ . '/../App/Src/Modules/Product/ProductRouter.php';
require_once __DIR__ . '/../App/Src/Core/Router/api.php';

$router->dispatch($req);
