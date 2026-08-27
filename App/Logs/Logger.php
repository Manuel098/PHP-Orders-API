<?php
namespace App\Logs;

class Logger
{
    private string $file;
    public function __construct() {
        $this->file = __DIR__ . sprintf( "/[%s]-error.log", date('Y-m-d'));
        if (!file_exists($this->file)) {
            touch($this->file);
        }
    }

    /**
     * Write on log file any error
     */
    public function error(string $message): void
    {
        $message = sprintf( "[%s] ERROR: %s \n", date('Y-m-d H:i:s'), $message );
        error_log($message, 3, $this->file);
    }
}