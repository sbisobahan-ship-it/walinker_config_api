<?php
require_once __DIR__ . '/../models/SendSms.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/security.php';

function handle_send_sms_request($route_parts, $conn) {
    $sendSms = new SendSms($conn);
    $userModel = new user($conn);

    // POST /send_sms -> create
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // validate auth token from header
        validate_admin_token($conn);

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $user_id = isset($input['user_id']) && $input['user_id'] !== '' ? (int)$input['user_id'] : null;
        $sms = isset($input['sms']) ? sanitize_string($input['sms']) : null;

        if (!$sms) {
            send_json(["error" => "sms required"], 400);
        }

        // check if user exists
        $user_exists = false;
        if ($user_id !== null) {
            $u = $userModel->getbyid($user_id);
            if ($u) $user_exists = true;
        }

        // If user exists, insert with that user_id; if not, insert with NULL user_id
        $insert_user_id = $user_exists ? $user_id : null;

        $res = $sendSms->create($insert_user_id, $sms);
        if (isset($res['success'])) {
            send_json(["status" => "success", "sms_id" => $res['sms_id']]);
        }

        send_json(["status" => "error", "message" => ($res['error'] ?? 'insert failed')], 500);
    }

    // GET /send_sms or /send_sms?app_id=xxx
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $app_id = isset($_GET['app_id']) ? trim($_GET['app_id']) : null;

        if ($app_id) {
            // return messages for this app_id PLUS those with null app_id/user
            $rows = $sendSms->getByAppIdIncludingNulls($app_id);
            send_json(["status" => "success", "data" => $rows]);
        }

        // Default: return only messages where associated user has no app_id OR sms.user_id IS NULL
        $rows = $sendSms->getPublicOrUsersWithNullApp();
        send_json(["status" => "success", "data" => $rows]);
    }

    // DELETE /send_sms -> delete by sms_id (requires admin token)
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // validate token
        validate_admin_token($conn);

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $sms_id = isset($input['sms_id']) ? intval($input['sms_id']) : 0;

        if ($sms_id <= 0) {
            send_json(["error" => "sms_id required"], 400);
        }

        $ok = $sendSms->delete($sms_id);
        if ($ok) {
            send_json(["status" => "success", "message" => "deleted"]);
        }

        send_json(["status" => "error", "message" => "not deleted or not found"], 404);
    }

    send_json(["error" => "Method not allowed"], 405);
}

?>
