<?php
require_once __DIR__ . '/../config/db_connect.php';

class SendSms {
    private $conn;
    private $table = 'send_sms';

    public function __construct($db_conn = null) {
        global $conn;
        $this->conn = $db_conn ?? $conn;
    }

    // Insert a new sms. $user_id may be null.
    public function create($user_id, $sms) {
        // Use conditional SQL so NULL user_id is inserted cleanly
        if ($user_id === null) {
            $sql = "INSERT INTO {$this->table} (user_id, sms) VALUES (NULL, ?)";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) return ["error" => "DB prepare failed"];
            $stmt->bind_param("s", $sms);
        } else {
            $sql = "INSERT INTO {$this->table} (user_id, sms) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) return ["error" => "DB prepare failed"];
            $stmt->bind_param("is", $user_id, $sms);
        }

        if ($stmt->execute()) {
            $id = $this->conn->insert_id;
            $stmt->close();
            return ["success" => true, "sms_id" => $id];
        }

        $err = $stmt->error;
        $stmt->close();
        return ["error" => $err];
    }

    // Get messages for a specific user_id (including public messages where user_id IS NULL)
    public function getForUser($user_id) {
        $sql = "SELECT s.sms_id, s.user_id, s.sms FROM {$this->table} s WHERE s.user_id = ? OR s.user_id IS NULL ORDER BY s.sms_id DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        $stmt->close();
        return $rows;
    }

    // Get all messages
    public function getAll() {
        $sql = "SELECT sms_id, user_id, sms FROM {$this->table} ORDER BY sms_id DESC";
        $res = $this->conn->query($sql);
        if (!$res) return [];
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        return $rows;
    }

    // Get public messages only (user_id IS NULL)
    public function getPublic() {
        $sql = "SELECT s.sms_id, s.user_id, s.sms FROM {$this->table} s WHERE s.user_id IS NULL ORDER BY s.sms_id DESC";
        $res = $this->conn->query($sql);
        if (!$res) return [];
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        return $rows;
    }

    // Get messages where the send_sms has no user OR the associated user's app_id IS NULL
    public function getPublicOrUsersWithNullApp() {
        $sql = "SELECT s.sms_id, s.user_id, s.sms FROM {$this->table} s LEFT JOIN users u ON s.user_id = u.user_id WHERE s.user_id IS NULL OR u.app_id IS NULL ORDER BY s.sms_id DESC";
        $res = $this->conn->query($sql);
        if (!$res) return [];
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        return $rows;
    }

    // Get messages where: send_sms has no user OR associated user's app_id IS NULL OR app_id matches given value
    public function getByAppIdIncludingNulls($app_id) {
        $sql = "SELECT s.sms_id, s.user_id, s.sms FROM {$this->table} s LEFT JOIN users u ON s.user_id = u.user_id WHERE s.user_id IS NULL OR u.app_id IS NULL OR u.app_id = ? ORDER BY s.sms_id DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param("s", $app_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        $stmt->close();
        return $rows;
    }

    // Delete sms by sms_id
    public function delete($sms_id) {
        $sql = "DELETE FROM {$this->table} WHERE sms_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("i", $sms_id);
        $ok = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $ok && $affected > 0;
    }
}
?>
