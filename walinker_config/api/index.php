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
// FCM Token Route
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
    'save_fcm_token' // ✅ নতুন FCM রাউট
    , 'send_sms'
];

// যদি কোনো রাউট ম্যাচ না হয়
if (!isset($route_parts[0]) || !in_array($route_parts[0], $allowed_routes)) {
    send_json(["error" => "Endpoint not found"], 404);
}
?>
