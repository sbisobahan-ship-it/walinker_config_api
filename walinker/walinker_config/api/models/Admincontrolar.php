<?php
class Admincontrolar {
    private $conn;
    private $table = "admin_controlar";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ✅ Get row (id = 1)
    public function getRow() {
        $sql = "SELECT admin_controlar_id, help, service, policy, updating, home_notification, server_activity
                FROM " . $this->table . " WHERE admin_controlar_id = 1 LIMIT 1";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    // ✅ Update allowed columns (row id = 1)
    public function updateColumn($column, $value) {
        $allowed = ["help", "service", "policy", "updating", "home_notification", "server_activity"];
        if (!in_array($column, $allowed)) {
            return false;
        }

        // নিরাপদ রাখতে সবসময় backtick ব্যবহার
        $col = "`" . $column . "`";

        $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET $col = ? WHERE admin_controlar_id = 1");
        $stmt->bind_param("s", $value);
        return $stmt->execute();
    }
}
?>
