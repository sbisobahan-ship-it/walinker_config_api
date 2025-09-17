<?php
class ReportLog {
    private $conn;
    private $table = "report_log";

    public function __construct($db) {
        $this->conn = $db;
    }

    // নতুন রিপোর্ট যোগ করা
    public function create($app_id, $group_id) {
        // ১. চেক করা যে একই app_id + group_id আগে আছে কিনা
        $check = $this->conn->prepare(
            "SELECT report_id FROM report_log WHERE app_id = ? AND group_id = ?"
        );
        if (!$check) {
            return ["error" => "Prepare failed (check): " . $this->conn->error];
        }

        $check->bind_param("ii", $app_id, $group_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            return ["error" => "এই ইউজার আগে থেকেই এই গ্রুপ রিপোর্ট করেছে"];
        }

        // ২. না থাকলে নতুন ইনসার্ট
        $stmt = $this->conn->prepare(
            "INSERT INTO report_log (app_id, group_id) VALUES (?, ?)"
        );
        if (!$stmt) {
            return ["error" => "Prepare failed (insert): " . $this->conn->error];
        }

        $stmt->bind_param("ii", $app_id, $group_id);

        if ($stmt->execute()) {
            // ❌ Group.reports update code রিমুভ করা হলো
            return ["success" => true, "message" => "রিপোর্ট রেকর্ড হয়েছে"];
        }

        return ["error" => "রিপোর্ট রেকর্ড করতে ব্যর্থ: " . $this->conn->error];
    }

    // নির্দিষ্ট group এর রিপোর্ট লগ দেখা
    public function getByGroup($group_id) {
        $stmt = $this->conn->prepare(
            "SELECT r.report_id, r.app_id, u.name as user_name, r.group_id, g.group_name
             FROM report_log r
             JOIN users u ON r.app_id = u.user_id
             JOIN `group` g ON r.group_id = g.group_id
             WHERE r.group_id = ?"
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
