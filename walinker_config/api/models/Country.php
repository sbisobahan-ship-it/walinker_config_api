<?php
class Country {
    private $conn;
    private $table = "country";

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET all
    public function getAllCountries() {
        $sql = "SELECT country_id, country_name FROM {$this->table}";
        return $this->conn->query($sql);
    }

    // GET by ID
    public function getCountryById($id) {
        $stmt = $this->conn->prepare("SELECT country_id, country_name FROM {$this->table} WHERE country_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // CREATE
    public function createCountry($name) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (country_name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }

    // UPDATE
    public function updateCountry($id, $name) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET country_name = ? WHERE country_id = ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // DELETE
    public function deleteCountry($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE country_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}
?>
