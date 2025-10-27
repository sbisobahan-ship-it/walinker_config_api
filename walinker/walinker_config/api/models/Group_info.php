<?php
class Group_info {
    private $conn;
    private $table = "group_info";

    public function __construct($db) {
        $this->conn = $db;
    }

    // চেক করে একই group_id আছে কি না
    public function exists($group_id) {
        $sql = "SELECT group_info_id FROM {$this->table} WHERE group_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // নতুন group_info ইনসার্ট করা
    public function create($group_id, $group_name, $image_link, $status) {
        $sql = "INSERT INTO {$this->table} (group_id, group_name, image_link, status) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return ["error" => $this->conn->error];
        }

        $stmt->bind_param("isss", $group_id, $group_name, $image_link, $status);
        if ($stmt->execute()) {
            return ["success" => true, "id" => $this->conn->insert_id];
        } else {
            return ["error" => $stmt->error];
        }
    }
}
