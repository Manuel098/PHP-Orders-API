<?php
namespace App\Src\Core\Router;

use App\Src\Core\HTTP\Response;
use App\Src\Core\HTTP\Request;
use App\Logs\Logger;

class CRUD {
    private Logger $logger;
    private array $routes;

    public function __construct() {
        $this->logger = new Logger();
    }

    // Define all HTTP methods on routes diccionary
    public function post( string $path, callable $method ):   void { $this->add('POST', $path, $method);    }
    public function get( string $path, callable $method ):    void { $this->add('GET', $path, $method);     }
    public function put( string $path, callable $method ):    void { $this->add('PUT', $path, $method);     }
    public function delete( string $path, callable $method ): void { $this->add('DELETE', $path, $method);  }

    /**
     * Dispatch callback depending the method called
     */
    public function dispatch(Request $request): void
    {
        $this->logger->request($request);
        $method = $request->method();
        $url = $request->url();

        $handler = $this->routes[$method][$url] ?? null;

        if (!$handler) {
            $this->logger->error(sprintf( "Failed to dispatch %s request on %s: \nException: %s", $request->getId(), $url, 'Page not found'));
            Response::json([ 'message' => 'Route not found' ], 404);
            return;
        }

        call_user_func($handler, $request);
    }

    /**
     * Register on diccionary the list of endpoints available
     */
    private function add( string $method, string $path, callable $handler ): void {
        $this->routes[$method][$path] = $handler;
    }
}