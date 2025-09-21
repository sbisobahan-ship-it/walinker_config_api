<?php
require_once __DIR__ . '/../helpers/security.php'; // sanitize_string

class Group {
    private $conn;
    private $table = "`group`"; // group MySQL keyword

    public function __construct($db) {
        $this->conn = $db;
    }

    // নতুন group create
    public function create($data) {
        if (!isset($data['user_id'], $data['categories'], $data['group_link'], $data['country'])) {
            return false;
        }

        $group_link = sanitize_string((string) $data['group_link']);

        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} 
            (user_id, categories, group_link, views, clicks, reports, post_at, country, post_panding, delete_group)
            VALUES (?, ?, ?, 0, 0, 0, NOW(), ?, 1, 0)"
        );
        if (!$stmt) {
            error_log("Prepare failed (create): " . $this->conn->error);
            return false;
        }

        $stmt->bind_param(
            "iisi",
            $data['user_id'],
            $data['categories'],
            $group_link,
            $data['country']
        );

        $result = $stmt->execute();
        if (!$result) {
            error_log("Execute failed (create): " . $stmt->error);
            $stmt->close();
            return false;
        }
        $stmt->close();
        return true;
    }

    // Duplicate link check
    public function isDuplicateLink($group_link) {
    $group_link = sanitize_string($group_link);
    $stmt = $this->conn->prepare("SELECT group_id FROM {$this->table} WHERE group_link = ? LIMIT 1");
    $stmt->bind_param("s", $group_link);
    $stmt->execute();
    $result = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $result;
    }

    // Rate limit: user_id অনুযায়ী কত সেকেন্ড আগে পোস্ট করেছে
    public function isRecentlyPosted($user_id, $seconds = 60) {
    $stmt = $this->conn->prepare("SELECT group_id FROM {$this->table} 
                      WHERE user_id = ? 
                      AND post_at >= (NOW() - INTERVAL ? SECOND) 
                      LIMIT 1");
    $stmt->bind_param("ii", $user_id, $seconds);
    $stmt->execute();
    $result = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $result;
    }

    // 72 ঘন্টা check & auto delete
    private function checkAndDeleteOldGroups() {
        $this->conn->query("
            UPDATE {$this->table}
            SET delete_group = 1
            WHERE delete_group = 0
              AND post_at <= NOW() - INTERVAL 72 HOUR
        ");
        $this->conn->query("
            DELETE FROM {$this->table}
            WHERE delete_group = 1
        ");
    }

    // সব গ্রুপ (pagination সহ) + country_name/category_name/group_name/min_reports filter
    public function getAll($page = 1, $limit = 10, $publicOnly = false, $country_name = null, $category_name = null, $group_name = null) {
        $this->checkAndDeleteOldGroups();
        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];
        $types = "";

        if ($publicOnly) {
            $where[] = "g.delete_group = 0 AND g.post_panding = 0";
        }

        if ($country_name) {
            $where[] = "c.country_name = ?";
            $params[] = sanitize_string($country_name);
            $types .= "s";
        }

        if ($category_name) {
            $where[] = "cat.category_name = ?";
            $params[] = sanitize_string($category_name);
            $types .= "s";
        }

        if ($group_name) {
            $where[] = "gi.group_name LIKE ?";
            $params[] = "%" . sanitize_string($group_name) . "%";
            $types .= "s";
        }

        // ✅ min_reports filter
        if (isset($_GET['min_reports'])) {
            $min_reports = intval($_GET['min_reports']);
            $where[] = "(SELECT COUNT(*) FROM report_log r WHERE r.group_id = g.group_id) >= ?";
            $params[] = $min_reports;
            $types .= "i";
        }

        $where_sql = "";
        if (count($where) > 0) {
            $where_sql = "WHERE " . implode(" AND ", $where);
        }

        $sql = "SELECT 
                    g.group_id, 
                    g.user_id,
                    cat.category_name AS categories,
                    g.group_link,
                    (SELECT COUNT(*) FROM view_log v WHERE v.group_id = g.group_id) AS views,
                    (SELECT COUNT(*) FROM click_log cl WHERE cl.group_id = g.group_id) AS clicks,
                    (SELECT COUNT(*) FROM report_log r WHERE r.group_id = g.group_id) AS reports,
                    g.post_at,
                    c.country_name AS country,
                    g.post_panding, 
                    g.delete_group,
                    gi.group_name,
                    gi.image_link,
                    gi.status
                FROM {$this->table} g
                JOIN country c ON g.country = c.country_id
                JOIN categories cat ON g.categories = cat.category_id
                LEFT JOIN group_info gi ON gi.group_id = g.group_id
                $where_sql
                ORDER BY g.post_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed (getAll): " . $this->conn->error);
            return [];
        }

        // bind params dynamically
        if ($types) {
            $types .= "ii"; // limit + offset
            $params[] = $limit;
            $params[] = $offset;
            $stmt->bind_param($types, ...$params);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    // single group by id
    public function getById($id, $publicOnly = false) {
        $this->checkAndDeleteOldGroups();

        $where = "";
        if ($publicOnly) {
            $where = "AND g.delete_group = 0 AND g.post_panding = 0";
        }

        $sql = "SELECT 
                    g.group_id, 
                    g.user_id,
                    cat.category_name AS categories,
                    g.group_link,
                    (SELECT COUNT(*) FROM view_log v WHERE v.group_id = g.group_id) AS views,
                    (SELECT COUNT(*) FROM click_log cl WHERE cl.group_id = g.group_id) AS clicks,
                    (SELECT COUNT(*) FROM report_log r WHERE r.group_id = g.group_id) AS reports,
                    g.post_at,
                    c.country_name AS country,
                    g.post_panding, 
                    g.delete_group,
                    gi.group_name,
                    gi.image_link,
                    gi.status
                FROM {$this->table} g
                JOIN country c ON g.country = c.country_id
                JOIN categories cat ON g.categories = cat.category_id
                LEFT JOIN group_info gi ON gi.group_id = g.group_id
                WHERE g.group_id = ? $where
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed (getById): " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result;
    }
}
