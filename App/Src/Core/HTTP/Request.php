<?php
namespace App\Src\Core\HTTP;

class Request {
    private string $requestId;
    private string $method;
    private string $url;
    private array  $body;
    private array  $timer;

    public function __construct(bool $test = false) {
        $this->requestId = bin2hex(random_bytes(16));
        $this->timer = [];

        if (!$test) {
            $this->method   = $_SERVER['REQUEST_METHOD']; 
            $this->url      = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ); 
            $this->body     = json_decode(file_get_contents('php://input') ?? '[]', true) ?? []; 
        }
    }

    public function getTrack(): array   { return $this->timer;      }
    public function getId():    string  { return $this->requestId;  }
    public function method():   string  { return $this->method;     }
    public function url():      string  { return $this->url;        }
    public function body():     array   { return $this->body;       }
    
    public function mSet($m):   void    { $this->method = $m;       }
    public function uSet($u):   void    { $this->url = $u;          }
    public function bSet($b):   void    { $this->body = $b;         }

    public function track(string $method, float $time) {
        $this->timer[$method] = number_format($time, 4);
    }
}