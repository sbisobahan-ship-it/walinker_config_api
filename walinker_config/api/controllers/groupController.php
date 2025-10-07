<?php
require_once __DIR__ . '/../models/Group.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/security.php'; // send_json, sanitize_string
require_once __DIR__ . '/../firebase/firebase.php'; // FCM function

function handle_group_request($route_parts, $conn) {
    $groupModel = new Group($conn);
    $userModel = new User($conn);

    // ----------------------
    // POST /group
    // ----------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) send_json(["error" => "Invalid JSON"], 400);

        if (!isset($input['app_id'], $input['categories'], $input['group_link'], $input['country'])) {
            send_json(["error"=>"Missing required fields"], 400);
        }

        $app_id = sanitize_string($input['app_id']);
        $group_link = sanitize_string($input['group_link']);

        // Flexible WhatsApp group or channel link validation
        if (!preg_match('/^https?:\/\/(www\.)?chat\.whatsapp\.com(\/invite)?\/[A-Za-z0-9]+$/', $group_link) &&
            !preg_match('/^https?:\/\/(www\.)?whatsapp\.com\/channel\/[A-Za-z0-9]+$/', $group_link)) {
            send_json(["error"=>"Invalid WhatsApp group or channel link"], 400);
        }

        $token = substr($group_link, strrpos($group_link, '/') + 1);
        if (strlen($token) < 10) {
            send_json(["error"=>"Invalid WhatsApp group or channel link token"], 400);
        }

        $user = $userModel->getUserByAppId($app_id);
        if (!$user) send_json(["error"=>"Invalid app_id"], 400);
        $user_id = $user['user_id'];

        if ($groupModel->isDuplicateLink($group_link)) {
            send_json(["error"=>"This group link already exists"], 409);
        }

        if ($groupModel->isRecentlyPosted($user_id, 60)) {
            send_json(["error"=>"You can post only once per minute"], 429);
        }

        $chk_cat = $conn->prepare("SELECT category_id FROM categories WHERE category_id = ?");
        $chk_cat->bind_param("i", $input['categories']);
        $chk_cat->execute();
        if ($chk_cat->get_result()->num_rows === 0) send_json(["error"=>"Invalid category"],400);

        $chk_country = $conn->prepare("SELECT country_id FROM country WHERE country_id = ?");
        $chk_country->bind_param("i", $input['country']);
        $chk_country->execute();
        if ($chk_country->get_result()->num_rows === 0) send_json(["error"=>"Invalid country"],400);

        $data = [
            'user_id' => $user_id,
            'categories' => $input['categories'],
            'group_link' => $group_link,
            'country' => $input['country']
        ];
        if ($groupModel->create($data)) {
            $title = "New Post!";
            $body  = "Please Approve or Reject: " . $group_link;
            sendFCMNotification($title, $body, $conn);

            send_json(["success"=>"Group created successfully"], 201);
        } else {
            send_json(["error"=>"Failed to create group"],500);
        }
    }

    // ----------------------
    // GET /group
    // ----------------------
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        // --- By app_id (custom route) ---
        if (isset($route_parts[1]) && $route_parts[1] === 'by_app_id') {
            $app_id = isset($route_parts[2]) ? sanitize_string($route_parts[2]) : null;
            if (!$app_id) send_json(["error"=>"app_id required"], 400);

            $user = $userModel->getUserByAppId($app_id);
            if (!$user) send_json(["error"=>"Invalid app_id"], 404);
            $user_id = $user['user_id'];

            $stmt = $conn->prepare("
                SELECT g.group_id, g.group_link, g.categories, g.post_panding, g.post_at
                FROM `group` g
                WHERE g.user_id = ? AND g.delete_group = 0
                ORDER BY g.post_at DESC
            ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            send_json([
                "user_id" => $user_id,
                "total_groups" => count($result),
                "data" => $result
            ]);
        }

        // --- By User ID (legacy) ---
        if (isset($route_parts[1]) && $route_parts[1] === 'by_user') {
            $user_id = isset($route_parts[2]) ? intval($route_parts[2]) : null;
            if (!$user_id) send_json(["error"=>"User ID required"], 400);

            $stmt = $conn->prepare("SELECT COUNT(*) AS total_groups FROM `group` WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            send_json([
                "user_id" => $user_id,
                "total_groups" => intval($result['total_groups'])
            ]);
        }

        // Pagination & filters
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($page < 1) $page = 1;

        $country_name = isset($_GET['country']) ? sanitize_string($_GET['country']) : null;
        $category_name = isset($_GET['categories']) ? sanitize_string($_GET['categories']) : null;
        $group_name = isset($_GET['group_name']) ? sanitize_string($_GET['group_name']) : null;

        if (isset($_GET['group_name'])) {
            $gn = trim($_GET['group_name']);
            if ($gn === '' || mb_strlen($gn) < 3) {
                send_json(["page"=>$page,"limit"=>10,"data"=>[]]);
            }
        }

        // Count logic
        $count = null;
        foreach ($_GET as $key => $value) {
            if (preg_match('/^count_(\d+)$/', $key, $matches)) {
                $days = intval($matches[1]);
                $stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM `group` WHERE post_at >= NOW() - INTERVAL ? DAY");
                $stmt_count->bind_param("i", $days);
                $stmt_count->execute();
                $count = $stmt_count->get_result()->fetch_assoc()['total'];
                break;
            } elseif ($key === 'count_all' || $key === 'count_*') {
                $stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM `group`");
                $stmt_count->execute();
                $count = $stmt_count->get_result()->fetch_assoc()['total'];
                break;
            }
        }

        if ($count !== null) {
            send_json(["count"=>$count]);
        }

        // Normal listing
        if (!isset($route_parts[1])) {
            $groups = $groupModel->getAll($page, 10, true, $country_name, $category_name, $group_name);
            send_json(["page"=>$page,"limit"=>10,"data"=>$groups]);
        } else {
            $id = intval($route_parts[1]);
            $group = $groupModel->getById($id, true);
            if ($group && $group['delete_group'] == 0) {
                send_json($group);
            } else {
                send_json(["error"=>"Group not found"],404);
            }
        }
    }

    // ----------------------
    // PATCH /group/{group_id}/delete
    // ----------------------
    if ($_SERVER['REQUEST_METHOD'] === 'PATCH' && isset($route_parts[1]) && isset($route_parts[2])) {
        $group_id = intval($route_parts[1]);
        if ($route_parts[2] !== "delete") send_json(["error"=>"Invalid PATCH action"],400);

        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;
        $bearerToken = null;
        if ($authHeader && preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
            $bearerToken = $matches[1];
        }

        $stmt_token = $conn->prepare("SELECT data FROM dummy_data WHERE id = 1 LIMIT 1");
        $stmt_token->execute();
        $dummy_token = $stmt_token->get_result()->fetch_assoc()['data'] ?? null;

        $isAuthorized = false;

        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['app_id'])) {
            $app_id = sanitize_string($input['app_id']);
            $stmt_check = $conn->prepare("
                SELECT g.group_id 
                FROM `group` g 
                JOIN users u ON g.user_id = u.user_id 
                WHERE g.group_id = ? AND u.app_id = ?
                LIMIT 1
            ");
            $stmt_check->bind_param("is", $group_id, $app_id);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            if ($result->num_rows > 0) $isAuthorized = true;
        }

        if ($bearerToken && $dummy_token && $bearerToken === $dummy_token) {
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            send_json(["error"=>"Unauthorized request"],403);
        }

        $stmt_update = $conn->prepare("UPDATE `group` SET delete_group = 1 WHERE group_id = ?");
        $stmt_update->bind_param("i", $group_id);
        $stmt_update->execute();

        if ($stmt_update->affected_rows > 0) {
            send_json(["success"=>"delete_group set to 1"]);
        } else {
            send_json(["error"=>"Group not found or already deleted"],404);
        }
    }
}
