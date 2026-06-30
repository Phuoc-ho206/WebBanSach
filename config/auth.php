<?php

/**
 * Cấu hình xác thực JWT và remember-me.
 * Đổi AUTH_JWT_SECRET khi triển khai production.
 */
if (!defined('AUTH_JWT_SECRET')) {
    define('AUTH_JWT_SECRET', 'WebBanSach-change-this-secret-in-production-2026');
}

if (!defined('AUTH_COOKIE_NAME')) {
    define('AUTH_COOKIE_NAME', 'auth_token');
}

if (!defined('AUTH_COOKIE_PATH')) {
    define('AUTH_COOKIE_PATH', '/WebBanSach/');
}

if (!defined('AUTH_REMEMBER_DAYS')) {
    define('AUTH_REMEMBER_DAYS', 30);
}

if (!defined('AUTH_SESSION_HOURS')) {
    define('AUTH_SESSION_HOURS', 24);
}
