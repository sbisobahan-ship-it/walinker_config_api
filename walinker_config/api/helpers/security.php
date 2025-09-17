<?php
function send_json($data, $code = 200) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function sanitize_string($str) {
    $s = trim($str);
    $s = preg_replace('/\s+/', ' ', $s);
    return filter_var($s, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
}

function validate_int($val) {
    if (!isset($val) || !is_numeric($val)) return false;
    return intval($val);
}

function allowlist_route($segment) {
    if ($segment === '') return true;
    return preg_match('/^[A-Za-z0-9_\-\.%]+$/', $segment);
}

function get_request_path() {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $script = dirname($_SERVER['SCRIPT_NAME']);
    if ($script !== '/' && strpos($uri, $script) === 0) {
        $uri = substr($uri, strlen($script));
    }
    $uri = preg_replace('#^/?index\\.php/?#', '', $uri);
    $uri = preg_replace('#/+#', '/', $uri);
    return trim($uri, '/');
}

function send_cors_headers() {
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    } else {
        header('Access-Control-Allow-Origin: *');
    }
    header('Access-Control-Allow-Methods: POST, GET, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}

/**
 * ✅ সার্ভার অ্যাক্টিভিটি চেক
 * যদি server_activity = 1 হয়, সব এপিআই বন্ধ হবে
 * শুধু admincontrolar রুট ছাড়া
 */
function check_server_status($conn, $route_parts) {
    if (isset($route_parts[0]) && $route_parts[0] === 'admincontrolar') {
        return; // admincontrolar সবসময় চালু থাকবে
    }

    $sql = "SELECT server_activity FROM admin_controlar WHERE admin_controlar_id = 1 LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        if ($row['server_activity'] == 1) {
            send_json(["error" => "Server is down for maintenance"], 503);
        }
    }
}

/**
 * ✅ PATCH এর জন্য admin token validate
 */
function validate_admin_token($conn) {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        send_json(["error" => "Authorization header missing"], 401);
    }

    $authHeader = $headers['Authorization'];
    if (stripos($authHeader, "Bearer ") !== 0) {
        send_json(["error" => "Invalid Authorization format"], 401);
    }

    $token = trim(substr($authHeader, 7));

    // dummy_data টেবিল থেকে token validate করা
    $sql = "SELECT data FROM dummy_data WHERE id = 1 LIMIT 1";
    $result = $conn->query($sql);
    if (!$result || $result->num_rows == 0) {
        send_json(["error" => "Token validation failed"], 500);
    }

    $row = $result->fetch_assoc();
    if ($token !== $row['data']) {
        send_json(["error" => "Invalid token"], 403);
    }
}
?>
