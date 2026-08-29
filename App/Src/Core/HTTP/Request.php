<?php
namespace App\Src\Core\HTTP;

class Request {
    private string $requestId;
    private string $method;
    private string $url;
    private array  $query;
    private array  $body;
    private array  $timer;
    private array  $params;

    public function __construct(bool $test = false) {
        $this->requestId = bin2hex(random_bytes(16));
        $this->timer = [];

        if (!$test) {
            $this->method   = $_SERVER['REQUEST_METHOD'];
            $this->url      = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
            $this->body     = json_decode(file_get_contents('php://input') ?? '[]', true) ?? [];

            parse_str(parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ), $query);
            $this->query = $query;
        }
    }
    // Getters
    public function getTrack(): array   { return $this->timer;      }
    public function getId():    string  { return $this->requestId;  }
    public function method():   string  { return $this->method;     }
    public function url():      string  { return $this->url;        }
    public function query():    array   { return $this->query;      }
    public function body():     array   { return $this->body;       }
    public function getParam(string $key) { return $this->params[$key]; }
    
    // Setters
    public function mSet($m):   void    { $this->method = $m;       }
    public function uSet($u):   void    { $this->url = $u;          }
    public function bSet($b):   void    { $this->body = $b;         }

    public function paramsSet(array $params): void { $this->params = $params; }
    public function track(string $method, float $time): void { $this->timer[$method] = number_format($time, 4); }
}