// এই কোডটি index.php তেই আছে, আলাদা ফাইলে না:
if (isset($route_parts[0]) && $route_parts[0] === 'save_fcm_token') {
    header("Content-Type: application/json; charset=UTF-8");

    $fcm_token = json_decode(file_get_contents("php://input"), true)['fcm_token'] 
                 ?? $_POST['fcm_token'] ?? null;

    if (!$fcm_token) {
        echo json_encode(["status"=>"error","message"=>"FCM token missing"]);
        exit;
    }

    $sql = "INSERT IGNORE INTO admin_tokens (fcm_token, updated_at) VALUES (?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fcm_token);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => $stmt->affected_rows > 0 
                         ? "Token inserted successfully" 
                         : "Token already exists"
        ]);
    } else {
        echo json_encode(["status"=>"error","message"=>$stmt->error]);
    }

    $stmt->close();
    exit;
}
