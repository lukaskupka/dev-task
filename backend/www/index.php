<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$debugMode = ($_SERVER['APP_ENV'] ?? getenv('APP_ENV')) === 'local'
    ? Tracy\Debugger::Development
    : Tracy\Debugger::Production;
Tracy\Debugger::enable($debugMode, __DIR__ . '/../logs');

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '') {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400');
    http_response_code(204);
    exit;
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($path !== '/graphql') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok']);
    exit;
}

// Parse GraphQL request: JSON body takes priority, then form params, then query string
$rawBody = file_get_contents('php://input');
$body = [];

if ($rawBody !== '' && $rawBody !== false) {
    $decoded = json_decode($rawBody, true);
    if (is_array($decoded)) {
        $body = $decoded;
    }
}

$query         = $body['query']         ?? $_REQUEST['query']         ?? '';
$variables     = $body['variables']     ?? null;
$operationName = $body['operationName'] ?? $_REQUEST['operationName'] ?? null;

$netteContainer = \App\Bootstrap::boot();

$container = \App\UserInterface\GraphQL\Bootstrap::createContainer($netteContainer);
/** @var \Rebing\GraphQL\GraphQL $graphql */
$graphql = $container->make(\Rebing\GraphQL\GraphQL::class);

$result = $graphql->query($query, is_array($variables) ? $variables : null, [
    'operationName' => $operationName,
]);

header('Content-Type: application/json');
echo json_encode($result);
