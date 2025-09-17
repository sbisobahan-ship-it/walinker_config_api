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

$handled = false;

// routers
require_once __DIR__ . '/routes/categories.php';
require_once __DIR__ . '/routes/country.php';
require_once __DIR__ . '/routes/users.php';
require_once __DIR__ . '/routes/group.php';
require_once __DIR__ . '/routes/click_log.php'; 
require_once __DIR__ . '/routes/view_log.php';  
require_once __DIR__ . '/routes/report_log.php'; 
require_once __DIR__ . '/routes/group_info.php';  
require_once __DIR__ . '/routes/admincontrolar.php';  // ✅

$allowed_routes = [
    'categories', 
    'country', 
    'users', 
    'group', 
    'click_log', 
    'view_log', 
    'report_log', 
    'group_info',
    'admincontrolar' // ✅
];

// যদি কোনো রাউট ম্যাচ না হয়
if (!isset($route_parts[0]) || !in_array($route_parts[0], $allowed_routes)) {
    send_json(["error" => "Endpoint not found"], 404);
}
?>
