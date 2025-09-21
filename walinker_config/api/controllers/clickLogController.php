<?php
require_once __DIR__ . '/../models/ClickLog.php';
require_once __DIR__ . '/../helpers/security.php';

function handle_click_log_request($route_parts, $conn) {
    $clickLog = new ClickLog($conn);

    // ===============================
    // POST /click_log → নতুন ক্লিক যোগ করা
    // ===============================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);

        $user_id  = isset($input['user_id']) ? (int)$input['user_id'] : null;
        $group_id = isset($input['group_id']) ? (int)$input['group_id'] : null;

        // Validation
        if ($user_id === null || $group_id === null) {
            send_json(["error" => "user_id এবং group_id প্রয়োজন"], 400);
        }

        // Create click log
        $result = $clickLog->create($user_id, $group_id);
        send_json($result);
        return;
    }

    // ===============================
    // GET /click_log?count → মোট ক্লিক সংখ্যা
    // ===============================
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['count'])) {
            $count = $clickLog->getTotalClicks();
            send_json(["total_clicks" => $count], 200);
            return;
        }

        // অন্য query এলে disallow
        send_json(["error" => "Invalid query parameter"], 400);
    }

    // ===============================
    // অন্য কোনো method disallow
    // ===============================
    send_json(["error" => "Method not allowed"], 405);
}
