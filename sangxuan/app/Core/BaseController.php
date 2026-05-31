<?php

/**
 * JsonResponse - Single Responsibility: trả JSON chuẩn
 */
class JsonResponse {
    public static function success($data = null, string $message = 'OK', int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
        exit;
    }

    public static function error(string $message, int $code = 400, $data = null): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $message, 'data' => $data]);
        exit;
    }
}

/**
 * BaseController - Open/Closed: mở rộng bằng kế thừa
 */
abstract class BaseController {
    protected function json($data, int $code = 200): void {
        JsonResponse::success($data, 'OK', $code);
    }

    protected function jsonError(string $msg, int $code = 400): void {
        JsonResponse::error($msg, $code);
    }

    protected function getJson(): array {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }

    protected function requireAuth(): void {
        session_start();
        if (empty($_SESSION['user_id'])) {
            JsonResponse::error('Unauthorized', 401);
        }
    }
}
