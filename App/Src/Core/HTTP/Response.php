<?php
namespace App\Src\Core\HTTP;

class Response {
    /**
     * Prepare response with status code
     * - Use
     *  Response::json([...], 200)
     */
    public static function json($data, $code = 200): void {
        if (PHP_SAPI !== 'cli') {
            http_response_code($code);
            header('Content-Type: application/json');
        }
        echo json_encode($data);
    }
}