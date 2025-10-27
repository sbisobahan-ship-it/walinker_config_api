<?php
require_once __DIR__ . '/helpers/security.php';
require_once __DIR__ . '/config/db_connect.php';

// Ensure DB connection is closed at the end of script
register_shutdown_function(function() use ($conn) {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
});

send_cors_headers();

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Allow: GET, POST, PATCH, PUT, DELETE');
    exit;
}

// Get request path
$path = get_request_path();
$route_parts = [];

if ($path !== '') {
    $route_parts = explode('/', $path);
    foreach ($route_parts as $p) {
        if (!allowlist_route($p)) {
            send_json(["error" => "Invalid path segment"], 400);
        }
    }
}

// ✅ সার্ভার অ্যাক্টিভিটি চেক (admincontrolar বাদে)
check_server_status($conn, $route_parts);

// ============================
// ADMIN FCM Token Route
// ============================
if (isset($route_parts[0]) && $route_parts[0] === 'save_fcm_token') {
    header("Content-Type: application/json; charset=UTF-8");

    // JSON বা POST থেকে token নাও
    $fcm_token = json_decode(file_get_contents("php://input"), true)['fcm_token'] 
                 ?? $_POST['fcm_token'] ?? null;

    if (!$fcm_token) {
        echo json_encode(["status"=>"error","message"=>"FCM token missing"]);
        exit;
    }

    // Insert or Update (ডুপ্লিকেট হলে updated_at update হবে)
    $sql = "INSERT INTO admin_tokens (fcm_token, updated_at) VALUES (?, NOW())
            ON DUPLICATE KEY UPDATE updated_at = NOW()";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "SQL prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("s", $fcm_token);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => $stmt->affected_rows > 0 
                         ? "Token inserted/updated successfully" 
                         : "Token already exists"
        ]);
    } else {
        echo json_encode(["status"=>"error","message"=>$stmt->error]);
    }

    $stmt->close();
    exit;
}

// ============================
// USER FCM Token Route (Prevent Duplicate for Same user_id)
// ============================
if (isset($route_parts[0]) && $route_parts[0] === 'save_user_fcm_token') {
    header("Content-Type: application/json; charset=UTF-8");

    // JSON বা POST থেকে ডেটা নাও
    $input = json_decode(file_get_contents("php://input"), true);

    $fcm_token = $input['fcm_token'] ?? $_POST['fcm_token'] ?? null;
    $user_id   = $input['user_id'] ?? $_POST['user_id'] ?? null;

    // ✅ Validation
    if (!$user_id || !$fcm_token) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing user_id or fcm_token"
        ]);
        exit;
    }

    // ✅ প্রথমে চেক করবো user_id আছে কিনা
    $check_sql = "SELECT id FROM user_fcm_tokens WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // আগেই আছে, তাহলে আপডেট করো
        $update_sql = "UPDATE user_fcm_tokens 
                       SET fcm_token = ?, update_at = NOW() 
                       WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $fcm_token, $user_id);

        if ($update_stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Token updated successfully for existing user"
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => $update_stmt->error]);
        }

        $update_stmt->close();
    } else {
        // না থাকলে নতুন ইনসার্ট করো
        $insert_sql = "INSERT INTO user_fcm_tokens (user_id, fcm_token, update_at) 
                       VALUES (?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("is", $user_id, $fcm_token);

        if ($insert_stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "New user token inserted successfully"
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => $insert_stmt->error]);
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
    exit;
}
// ============================
// ADMIN CONTROLAR GET ROUTE
// ============================
if (isset($route_parts[0]) && $route_parts[0] === 'admin_controlar' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header("Content-Type: application/json; charset=UTF-8");

    // ✅ শুধু প্রথম রোটি নিয়ে আসবে
    $sql = "SELECT admin_controlar_id, help, service, policy, updating, server_activity, home_notification 
            FROM admin_controlar 
            ORDER BY admin_controlar_id ASC 
            LIMIT 1";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "status" => "success",
            "data" => $row
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No data found in admin_controlar table"
        ]);
    }

    exit;
}

// ============================
// অন্যান্য routers
// ============================
require_once __DIR__ . '/routes/categories.php';
require_once __DIR__ . '/routes/country.php';
require_once __DIR__ . '/routes/users.php';
require_once __DIR__ . '/routes/group.php';
require_once __DIR__ . '/routes/click_log.php'; 
require_once __DIR__ . '/routes/view_log.php';  
require_once __DIR__ . '/routes/report_log.php'; 
require_once __DIR__ . '/routes/group_info.php';  
require_once __DIR__ . '/routes/admincontrolar.php';
require_once __DIR__ . '/routes/send_sms.php';

// Allowed routes
$allowed_routes = [
    'categories', 
    'country', 
    'users', 
    'group', 
    'click_log', 
    'view_log', 
    'report_log', 
    'group_info',
    'admincontrolar',
    'send_sms',
    'save_fcm_token',
    'save_user_fcm_token',
    'admin_controlar' 
];

// যদি কোনো রাউট ম্যাচ না হয়
if (!isset($route_parts[0]) || !in_array($route_parts[0], $allowed_routes)) {
    send_json(["error" => "Endpoint not found"], 404);
}
?>