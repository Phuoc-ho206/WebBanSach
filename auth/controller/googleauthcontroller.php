<?php

define('GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI', 'http://localhost/WebBanSach/auth/pages/login.php');

class googleauthcontroller
{

    // Tạo URL redirect sang Google
    public static function getLoginUrl(): string
    {
        $params = http_build_query([
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
        ]);
        return 'https://accounts.google.com/o/oauth2/auth?' . $params;
    }

    // Xử lý sau khi Google redirect về
    public static function handleCallback(): ?array
    {
        if (empty($_GET['code']))
            return null;

        // Đổi code lấy access token
        $token = self::getAccessToken($_GET['code']);
        if (empty($token['access_token']))
            return null;

        // Lấy thông tin user
        $user = self::getUserInfo($token['access_token']);
        if (empty($user['email']))
            return null;

        return [
            'name' => $user['name'] ?? '',
            'email' => $user['email'] ?? '',
            'avatar' => $user['picture'] ?? '',
            'google_id' => $user['id'] ?? '',
        ];
    }

    // --- Private helpers ---

    private static function getAccessToken(string $code): array
    {
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'code' => $code,
                'client_id' => GOOGLE_CLIENT_ID,
                'client_secret' => GOOGLE_CLIENT_SECRET,
                'redirect_uri' => GOOGLE_REDIRECT_URI,
                'grant_type' => 'authorization_code',
            ]),
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true) ?? [];
    }

    private static function getUserInfo(string $access_token): array
    {
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $access_token],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true) ?? [];
    }
}