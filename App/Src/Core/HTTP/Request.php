<?php
namespace App\Src\Core\HTTP;

class Request {
    private string $requestId;

    public function __construct() {
        $this->requestId = bin2hex(random_bytes(16));
    }

    public function getId():  string  { return $this->requestId; }
    public function method(): string  { return $_SERVER['REQUEST_METHOD']; }
    public function url():    string  { return parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ); }
    public function body():   array   { return json_decode(file_get_contents('php://input') ?? '[]', true) ?? []; }
}