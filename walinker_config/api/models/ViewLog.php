<?php
require_once __DIR__ . '/User.php'; // User model কল করা হলো

class ViewLog {
    private $conn;
    private $table = "view_log";

    public function __construct($db) {
        $this->conn = $db;
    }

    // নতুন ভিউ যোগ করা
    public function create($app_id, $group_id) {
        // ১. চেক করা যে একই app_id + group_id আগে আছে কিনা
        $check = $this->conn->prepare(
            "SELECT view_id FROM view_log WHERE app_id = ? AND group_id = ?"
        );
        if (!$check) {
            return ["error" => "Prepare failed (check): " . $this->conn->error];
        }

        $check->bind_param("ii", $app_id, $group_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            return ["error" => "এই ইউজার আগে থেকেই এই গ্রুপ ভিউ করেছে"];
        }

        // ২. না থাকলে নতুন ইনসার্ট করবো
        $stmt = $this->conn->prepare(
            "INSERT INTO view_log (app_id, group_id) VALUES (?, ?)"
        );
        if (!$stmt) {
            return ["error" => "Prepare failed (insert): " . $this->conn->error];
        }

        $stmt->bind_param("ii", $app_id, $group_id);

        if ($stmt->execute()) {
            // ✅ User এর last_active আপডেট
            $userModel = new User($this->conn);
            $userModel->updateLastActive($app_id);

            return [
                "success" => true,
                "message" => "ভিউ রেকর্ড হয়েছে এবং ইউজারের last_active আপডেট হয়েছে"
            ];
        }

        return ["error" => "ভিউ রেকর্ড করতে ব্যর্থ: " . $this->conn->error];
    }

    // নির্দিষ্ট group এর ভিউ লগ দেখা
    public function getByGroup($group_id) {
        $stmt = $this->conn->prepare(
            "SELECT v.view_id, v.app_id, u.name as user_name, v.group_id, g.group_name
             FROM view_log v
             JOIN users u ON v.app_id = u.user_id
             JOIN `group` g ON v.group_id = g.group_id
             WHERE v.group_id = ?"
        );

        if (!$stmt) {
            return ["error" => "Prepare failed (getByGroup): " . $this->conn->error];
        }

        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }

        return $logs;
    }
}
