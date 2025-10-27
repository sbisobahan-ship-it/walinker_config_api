<?php
require_once __DIR__ . '/../models/ReportLog.php';
require_once __DIR__ . '/../helpers/security.php';

function handle_report_log_request($route_parts, $conn) {
    $reportLog = new ReportLog($conn);

    // -------------------
    // POST /report_log
    // -------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);

        $app_id = validate_int($input['app_id'] ?? null);
        $group_id = validate_int($input['group_id'] ?? null);

        if (!$app_id || !$group_id) {
            send_json(["error" => "app_id এবং group_id প্রয়োজন"], 400);
        }

        $result = $reportLog->create($app_id, $group_id);
        send_json($result);
    }

    // অন্য মেথড হলে
    send_json(["error" => "Method not allowed"], 405);
}
