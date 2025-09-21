<?php
require_once __DIR__ . '/../models/Group_info.php';
require_once __DIR__ . '/../helpers/security.php';

function handle_group_info_request($route_parts, $conn) {
    $groupInfo = new Group_info($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // -------- POST section --------
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) send_json(["error" => "Invalid JSON input"], 400);

        $group_id   = validate_int($input['group_id'] ?? null);
        $group_name = sanitize_string($input['group_name'] ?? '');
        $image_link = sanitize_string($input['image_link'] ?? '');
        $status     = sanitize_string($input['status'] ?? '');

        if (!$group_id || !$group_name || !$status) send_json(["error" => "Missing required fields"], 400);

        if ($groupInfo->exists($group_id)) send_json(["error" => "This group_id already has a record"], 409);

        $conn->begin_transaction();
        try {
            // Update post_panding
            $sql = "UPDATE `group` SET post_panding = 0 WHERE group_id = ? AND post_panding = 1";
            $stmt = $conn->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: ".$conn->error);
            $stmt->bind_param("i", $group_id);
            if (!$stmt->execute()) throw new Exception("Execute failed: ".$stmt->error);
            if ($stmt->affected_rows === 0) throw new Exception("Post_pending update failed: either group_id not found or already 0");

            // Insert into group_info
            $res = $groupInfo->create($group_id, $group_name, $image_link, $status);
            if (isset($res["error"])) throw new Exception($res["error"]);

            $conn->commit();
            send_json([
                "success" => true,
                "group_info_id" => $res["id"],
                "updated_group_id" => $group_id
            ], 201);

        } catch(Exception $e) {
            $conn->rollback();
            send_json(["error" => $e->getMessage()], 500);
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // -------- GET section --------
        try {
            $sql = "SELECT group_id, user_id, categories, group_link, views, clicks, reports, post_at, country, post_panding
                    FROM `group`
                    WHERE post_panding = 1 AND delete_group = 0";
            $result = $conn->query($sql);
            if (!$result) throw new Exception("Query failed: " . $conn->error);

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            send_json([
                "success" => true,
                "count" => count($data),
                "groups" => $data
            ], 200);

        } catch(Exception $e) {
            send_json(["error" => $e->getMessage()], 500);
        }

    } else {
        send_json(["error" => "Method not allowed"], 405);
    }
}
