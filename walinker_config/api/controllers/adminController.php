<?php
require_once __DIR__ . '/../models/Admincontrolar.php';
require_once __DIR__ . '/../helpers/security.php';

function handle_admin_request($route_parts, $conn) {
    $adminModel = new Admincontrolar($conn);
    $method = $_SERVER['REQUEST_METHOD'];

    // ------------------------
    // GET /admincontrolar
    // ------------------------
    if ($method === 'GET') {
        $row = $adminModel->getRow();
        if ($row) {
            send_json($row);
        } else {
            send_json(["error" => "No data found"], 404);
        }
    }

    // ------------------------
    // PATCH /admincontrolar
    // ------------------------
    if ($method === 'PATCH') {
        // ✅ PATCH করার আগে token validate
        validate_admin_token($conn);

        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input || !is_array($input)) {
            send_json(["error" => "Invalid input"], 400);
        }

        $updated = [];
        foreach ($input as $column => $value) {
            if ($adminModel->updateColumn($column, $value)) {
                $updated[] = $column;
            }
        }

        if (!empty($updated)) {
            $row = $adminModel->getRow();
            send_json([
                "success" => true,
                "updated_columns" => $updated,
                "data" => $row
            ]);
        } else {
            send_json(["error" => "No valid fields updated"], 400);
        }
    }

    send_json(["error" => "Method not allowed"], 405);
}
?>
