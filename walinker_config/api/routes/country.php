<?php
if (!isset($route_parts[0]) || $route_parts[0] !== 'country') return false;

require_once __DIR__ . '/../controllers/countryController.php';
$controller = new CountryController($conn);

// GET METHODS
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($route_parts[1]) || $route_parts[1] === '') {
        $controller->getAll();
        $handled = true;
        return;
    }

    $id = validate_int($route_parts[1]);
    if ($id !== false) {
        $controller->getById($id);
        $handled = true;
        return;
    }
}

// POST METHOD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->create($data, $conn);
    $handled = true;
    return;
}

// PATCH METHOD
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    if (!isset($route_parts[1])) send_json(["error" => "Country ID required"], 400);
    $id = validate_int($route_parts[1]);
    if ($id === false) send_json(["error" => "Invalid ID"], 400);

    $data = json_decode(file_get_contents('php://input'), true);
    $controller->update($id, $data, $conn);
    $handled = true;
    return;
}

// DELETE METHOD
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($route_parts[1])) send_json(["error" => "Country ID required"], 400);
    $id = validate_int($route_parts[1]);
    if ($id === false) send_json(["error" => "Invalid ID"], 400);

    $controller->delete($id, $conn);
    $handled = true;
    return;
}

// Method not allowed
send_json(["error" => "Method Not Allowed"], 405);
?>
