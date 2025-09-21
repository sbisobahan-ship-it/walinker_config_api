<?php
require_once __DIR__ . '/../config/db_connect.php';

class user {
    private $conn;
    private $table = "users";

    public function __construct($db_conn = null) {
        global $conn;
        $this->conn = $db_conn ?? $conn;
    }

    // ✅ user_id দিয়ে ইউজার খুঁজে আনা
    public function getbyid($user_id) {
        $stmt = $this->conn->prepare("SELECT user_id, app_id, ip_address, is_disable, last_active 
                                      FROM {$this->table} 
                                      WHERE user_id = ? 
                                      LIMIT 1");
        if (!$stmt) return false;

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();

        return $user ?: false;
    }

    // ✅ app_id দিয়ে ইউজার খুঁজে আনা
    public function getuserbyappid($app_id) {
        $stmt = $this->conn->prepare("SELECT user_id, app_id 
                                      FROM {$this->table} 
                                      WHERE app_id = ? 
                                      LIMIT 1");
        if (!$stmt) return false;

        $stmt->bind_param("s", $app_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();

        return $user ?: false;
    }

    // ✅ একই ip এক মিনিটে পোস্ট করলে ব্লক
    public function ippostedrecently($ip) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as cnt 
                                      FROM {$this->table} 
                                      WHERE ip_address = ? 
                                      AND created_at >= (NOW() - INTERVAL 1 MINUTE)");
        if (!$stmt) return false;

        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $res['cnt'] > 0;
    }

    // ✅ নতুন ইউজার ইনসার্ট এবং insert_id রিটার্ন
    public function insertuser($app_id, $ip) {
        if (!$app_id || !$ip) return false;

        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (app_id, created_at, is_disable, ip_address) 
                                      VALUES (?, NOW(), 0, ?)");
        if (!$stmt) return false;

        $stmt->bind_param("ss", $app_id, $ip);
        if ($stmt->execute()) {
            $id = $this->conn->insert_id;
            $stmt->close();
            return $id; // numeric user_id রিটার্ন
        }
        $stmt->close();
        return false;
    }

    // ✅ is_disable = 1 row মুছে ফেলা
    public function deleteifdisabled() {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE is_disable = 1");
        if (!$stmt) return false;
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // ✅ ইউজারের last_active আপডেট ৫ মিনিট interval সহ
    public function updatelastactive($user_id) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table} 
             SET last_active = NOW() 
             WHERE user_id = ? 
             AND (last_active IS NULL OR last_active <= (NOW() - INTERVAL 5 MINUTE))"
        );
        if (!$stmt) return false;
        $stmt->bind_param("i", $user_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // ✅ 14 দিনের বেশি inactive ইউজারদের disable করা
    public function disableinactiveusers($days = 14) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table} 
             SET is_disable = 1 
             WHERE last_active < (NOW() - INTERVAL ? DAY)
             AND is_disable = 0"
        );
        if (!$stmt) return false;
        $stmt->bind_param("i", $days);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // ✅ মোট ইউজার সংখ্যা বের করবে
    public function countallusers() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as cnt FROM {$this->table}");
        if (!$stmt) return false;
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res['cnt'] ?? 0;
    }

    // ✅ শেষ X দিনে তৈরি হওয়া ইউজার সংখ্যা
    public function countusersbydays($days) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as cnt 
                                      FROM {$this->table} 
                                      WHERE created_at >= (NOW() - INTERVAL ? DAY)");
        if (!$stmt) return false;
        $stmt->bind_param("i", $days);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res['cnt'] ?? 0;
    }
}
?>
