<?php

require_once __DIR__ . '/init_session.php';

function env_or_dotenv($key) {
    $v = getenv($key);
    if ($v !== false && $v !== '') return $v;

    $dot = __DIR__ . '/../.env';
    if (!file_exists($dot)) return null;
    $lines = file($dot, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (!str_contains($line, '=')) continue;
        [$k, $val] = explode('=', $line, 2);
        if (trim($k) === $key) return trim($val, " \t\n\r\0\x0B\"");
    }
    return null;
}

$client_id = env_or_dotenv('GOOGLE_CLIENT_ID');
$base = env_or_dotenv('BASE_URL') ?: 'http://localhost/taskdesk';
$redirect_uri = rtrim($base, '/') . '/connect/google_callback.php';
$scope = 'openid email profile';

if (empty($client_id)) {
    http_response_code(500);
    echo "Google Client ID not configured. Set GOOGLE_CLIENT_ID in environment or .env file.";
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

$params = [
    'response_type' => 'code',
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'scope' => $scope,
    'access_type' => 'offline',
    'prompt' => 'consent',
    'state' => $state
];

$url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

header("Location: $url");
exit;
