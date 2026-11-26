<?php
$root = dirname(__DIR__);
chdir($root);

$path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$path = '/' . ltrim((string) $path, '/');

if ($path === '/' || $path === '') {
    $path = '/index.php';
}

$safePath = rawurldecode($path);
$target = $root . $safePath;

if (str_contains($safePath, '..') || str_ends_with($safePath, '/')) {
    http_response_code(404);
    exit('Not found');
}

if (!is_file($target)) {
    $candidate = $root . $safePath . '.php';
    if (is_file($candidate)) {
        $target = $candidate;
    } else {
        http_response_code(404);
        exit('Not found');
    }
}

if (str_ends_with($target, '.php')) {
    $_SERVER['SCRIPT_NAME'] = $safePath;
    $_SERVER['PHP_SELF'] = $safePath;
    require $target;
    exit;
}

http_response_code(404);
echo 'Not found';
