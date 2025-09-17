<?php
class ClickLog {
    private $conn;
    private $table = "click_log";

    public function __construct($db) {
        $this->conn = $db;
    }

    // নতুন ক্লিক যোগ করা (POST)
    public function create($user_id, $group_id) {
        // আগে চেক করা যে একই user_id + group_id আগে আছে কিনা
        $check = $this->conn->prepare(
            "SELECT click_id FROM click_log WHERE app_id = ? AND group_id = ?"
        );
        if (!$check) {
            return ["error" => "Prepare failed (check): " . $this->conn->error];
        }

        $check->bind_param("ii", $user_id, $group_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            return ["error" => "এই ইউজার ইতিমধ্যেই এই গ্রুপে ক্লিক করেছে"];
        }

        // নতুন ইনসার্ট
        $stmt = $this->conn->prepare(
            "INSERT INTO click_log (app_id, group_id) VALUES (?, ?)"
        );
        if (!$stmt) {
            return ["error" => "Prepare failed (insert): " . $this->conn->error];
        }

        $stmt->bind_param("ii", $user_id, $group_id);

        if ($stmt->execute()) {
            return ["success" => true, "message" => "Click record created"];
        } else {
            $error_msg = $stmt->error;
            $error_code = $this->conn->errno;

            if ($error_code === 1452) {
                if (strpos($error_msg, 'app_id') !== false) {
                    return ["error" => "Invalid user_id: $user_id (user not found)"];
                } elseif (strpos($error_msg, 'group_id') !== false) {
                    return ["error" => "Invalid group_id: $group_id (group not found)"];
                }
                return ["error" => "Invalid reference: user_id or group_id not found"];
            }

            return ["error" => "ক্লিক রেকর্ড করতে ব্যর্থ: " . $error_msg];
        }
    }

    // মোট ক্লিক সংখ্যা বের করা (GET ?count)
    public function getTotalClicks() {
        $sql = "SELECT COUNT(click_id) AS total FROM click_log";
        $result = $this->conn->query($sql);

        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['total'];
        }

        return 0;
    }
}
