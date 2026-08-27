<?php
namespace App\Logs;

use App\Src\Core\HTTP\Request;

class Logger
{
    private string $file;

    private function checkFile(): void {
        if (!file_exists($this->file)) { touch($this->file); }
    }

    /**
     * Write on log file any error
     */
    public function error(string $message): void
    {
        $this->file = __DIR__ . sprintf( "/[%s]-error.log", date('Y-m-d'));
        $this->checkFile();
        $message = sprintf( "[%s] ERROR: %s \n", date('Y-m-d H:i:s'), $message );
        error_log($message, 3, $this->file);
    }
    /**
     * Write on log file request information
     */
    public function request(Request $req): void
    {
        $this->file = __DIR__ . sprintf( "/[%s]-requests.log", date('Y-m-d'));
        $this->checkFile();
        $message = sprintf( "[%s] Request: %s \n", $req->getId(), json_encode([
            'method' => $req->method(),
            'url'    => $req->url(),
            'body'   => $req->body(),
        ]) );
        error_log($message, 3, $this->file);
    }
}