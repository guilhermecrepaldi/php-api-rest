<?php
class Response {
    public static function json(array $data, int $status = 200): void {
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
