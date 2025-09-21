<?php
require_once __DIR__ . '/../controllers/categoriesController.php';
$controller = new CategoriesController();

$path = $route_parts ?? [];
if (count($path) === 0 || $path[0] !== 'categories') return false;

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (count($path) === 1) $controller->index();
    elseif (count($path) === 2) $controller->show($path[1]);
    else send_json(["error" => "Not found"], 404);
    return;
}

// POST
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->create($data, $conn);
    return;
}

// PATCH
if ($method === 'PATCH') {
    if (!isset($path[1])) send_json(["error" => "Category ID required"], 400);
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->update($path[1], $data, $conn);
    return;
}

// DELETE
if ($method === 'DELETE') {
    if (!isset($path[1])) send_json(["error" => "Category ID required"], 400);
    $controller->delete($path[1], $conn);
    return;
}

// Method not allowed
header('Allow: GET, POST, PATCH, DELETE');
send_json(["error" => "Method not allowed"], 405);
?>
