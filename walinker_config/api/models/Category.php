<?php
require_once __DIR__ . '/../config/db_connect.php';

class Category {
    private $conn;
    private $table = 'categories';

    public function __construct($db_conn = null) {
        global $conn;
        $this->conn = $db_conn ?? $conn;
    }

    // GET all
    public function getAll() {
        $sql = "SELECT category_id, category_name FROM {$this->table} ORDER BY category_id ASC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) $rows[] = $row;
        $stmt->close();
        return $rows;
    }

    // GET by ID
    public function getById($id) {
        $sql = "SELECT category_id, category_name FROM {$this->table} WHERE category_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    // CREATE
    public function create($name) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (category_name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) return $this->conn->insert_id;
        return false;
    }

    // UPDATE
    public function update($id, $name) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET category_name = ? WHERE category_id = ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // DELETE
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE category_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}
?>
