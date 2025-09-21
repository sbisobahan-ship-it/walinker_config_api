<?php
require_once __DIR__ . '/../models/Country.php';
require_once __DIR__ . '/../helpers/security.php';

class CountryController {
    private $model;

    public function __construct($db) {
        $this->model = new Country($db);
    }

    // GET /country
    public function getAll() {
        $result = $this->model->getAllCountries();
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        send_json($data);
    }

    // GET /country/{id}
    public function getById($id) {
        $result = $this->model->getCountryById($id);
        $row = $result->fetch_assoc();
        if ($row) {
            send_json($row);
        } else {
            send_json(["error" => "Country not found"], 404);
        }
    }

    // POST /country
    public function create($data, $conn) {
        validate_admin_token($conn); // token validate
        $name = sanitize_string($data['country_name'] ?? '');
        if ($name === '') send_json(["error" => "Country name required"], 400);

        $id = $this->model->createCountry($name);
        if ($id !== false) {
            send_json(["success" => true, "country_id" => $id]);
        } else {
            send_json(["error" => "Failed to create country"], 500);
        }
    }

    // PATCH /country/{id}
    public function update($id, $data, $conn) {
        validate_admin_token($conn); // token validate
        $name = sanitize_string($data['country_name'] ?? '');
        if ($name === '') send_json(["error" => "Country name required"], 400);

        $updated = $this->model->updateCountry($id, $name);
        if ($updated) {
            send_json(["success" => true]);
        } else {
            send_json(["error" => "Country not found or no changes made"], 404);
        }
    }

    // DELETE /country/{id}
    public function delete($id, $conn) {
        validate_admin_token($conn); // token validate
        $deleted = $this->model->deleteCountry($id);
        if ($deleted) {
            send_json(["success" => true]);
        } else {
            send_json(["error" => "Country not found"], 404);
        }
    }
}
?>
