<?php  
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/security.php';

function handle_users_request($route_parts, $conn) {
    $usermodel = new user($conn);

    // ✅ ৭ দিনের বেশি inactive ইউজাররা disable হবে
    $usermodel->disableinactiveusers(7);

    // -------------------
    // POST /users → নতুন ইউজার তৈরি
    // -------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($route_parts[1]) || $route_parts[1] === '')) {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['app_id']) || empty(trim($input['app_id']))) {
            send_json(["error" => "app_id is required"], 400);
        }
        $app_id = sanitize_string($input['app_id']);
        if (strlen($app_id) !== 36) {
            send_json(["error" => "invalid app_id length"], 400);
        }

        $existinguser = $usermodel->getuserbyappid($app_id);
        if ($existinguser) {
            send_json(["error" => "app_id already exists"], 409);
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        if (!$ip) {
            send_json(["error" => "ip address not detected"], 400);
        }

        if ($usermodel->ippostedrecently($ip)) {
            send_json(["error" => "too many requests from this ip within 1 minute"], 429);
        }

        $usermodel->deleteifdisabled();

        $newuserid = $usermodel->insertuser($app_id, $ip);

        if ($newuserid) {
            send_json([
                "success" => true,
                "user_id" => (int)$newuserid
            ], 201);
        } else {
            send_json(["error" => "failed to add user"], 500);
        }
    }

// ✅ POST /users/validate → লোকাল app_id এখনো ভ্যালিড কিনা চেক
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($route_parts[1]) && $route_parts[1] === 'validate') {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['app_id']) || empty(trim($input['app_id']))) {
            send_json(["error" => "app_id is required"], 400);
        }

        $app_id = sanitize_string($input['app_id']);
        if (strlen($app_id) !== 36) {
            send_json(["error" => "invalid app_id length"], 400);
        }

        $user = $usermodel->getuserbyappid($app_id);
        if ($user && (int)$user['user_id'] > 0) {
            send_json([
                "valid" => true,
                "user_id" => (int)$user['user_id']
            ], 200);
        } else {
            send_json([
                "valid" => false,
                "error" => "app_id not found"
            ], 404);
        }
    }
    
    // -------------------
    // GET /users → ইউজার কাউন্ট
    // -------------------
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && (!isset($route_parts[1]) || $route_parts[1] === '')) {
        if (isset($_GET['days']) && is_numeric($_GET['days'])) {
            $days = (int) $_GET['days'];
            if ($days <= 0) {
                send_json(["error" => "days must be greater than 0"], 400);
            }
            $count = $usermodel->countusersbydays($days);
            send_json(["total_new_users" => (int)$count], 200);
        }

        $count = $usermodel->countallusers();
        send_json(["total_users" => (int)$count], 200);
    }

    // -------------------
    // GET /users/details → Authorization token + selected fields
    // -------------------
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($route_parts[1]) && $route_parts[1] === 'details') {

        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            send_json(["error" => "Authorization header missing"], 401);
        }

        $authHeader = $headers['Authorization'];
        if (stripos($authHeader, "Bearer ") !== 0) {
            send_json(["error" => "Invalid Authorization format"], 401);
        }

        $token = trim(substr($authHeader, 7));

        $stmt = $conn->prepare("SELECT data FROM dummy_data WHERE id = 1 LIMIT 1");
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$res || $token !== $res['data']) {
            send_json(["error" => "Invalid token"], 403);
        }

        $stmt = $conn->prepare("SELECT user_id, created_at, last_active FROM users WHERE is_disable = 0");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();

        send_json(["users" => $users], 200);
    }

    // -------------------
    // PATCH /users/disable → only disable (is_disable=1)
    // -------------------
    if ($_SERVER['REQUEST_METHOD'] === 'PATCH' && isset($route_parts[1]) && $route_parts[1] === 'disable') {

        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            send_json(["error" => "Authorization header missing"], 401);
        }

        $authHeader = $headers['Authorization'];
        if (stripos($authHeader, "Bearer ") !== 0) {
            send_json(["error" => "Invalid Authorization format"], 401);
        }

        $token = trim(substr($authHeader, 7));

        $stmt = $conn->prepare("SELECT data FROM dummy_data WHERE id = 1 LIMIT 1");
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$res || $token !== $res['data']) {
            send_json(["error" => "Invalid token"], 403);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['user_id']) || !is_numeric($input['user_id'])) {
            send_json(["error" => "user_id is required and must be numeric"], 400);
        }

        $user_id = (int)$input['user_id'];

        // Check if user exists
        $user = $usermodel->getbyid($user_id);
        if (!$user) {
            send_json(["error" => "User not found"], 404);
        }

        // Check if already disabled
        if ((int)$user['is_disable'] === 1) {
            send_json(["error" => "User already disabled"], 400);
        }

        // Only allow disable (is_disable=1)
        $is_disable = 1;

        $stmt = $conn->prepare("UPDATE users SET is_disable = ? WHERE user_id = ?");
        $stmt->bind_param("ii", $is_disable, $user_id);

        if ($stmt->execute()) {
            $stmt->close();
            send_json([
                "success" => true,
                "user_id" => $user_id,
                "is_disable" => $is_disable
            ], 200);
        } else {
            $stmt->close();
            send_json(["error" => "Failed to update is_disable"], 500);
        }
    }

    // অন্য সব মেথড ব্লক
    send_json(["error" => "method not allowed"], 405);
}
?>

