<?php
require_once __DIR__ . '/../helpers/security.php';
require_once __DIR__ . '/../models/Category.php';

class CategoriesController {
    private $model;

    public function __construct() {
        $this->model = new Category();
    }

    // GET /categories
    public function index() {
        $data = $this->model->getAll();
        if ($data === false) {
            send_json(["error" => "Unable to fetch categories"], 500);
        }
        send_json($data);
    }

    // GET /categories/{id}
    public function show($id) {
        $id = validate_int($id);
        if ($id === false) {
            send_json(["error" => "Invalid id"], 400);
        }
        $row = $this->model->getById($id);
        if ($row === null) {
            send_json(["error" => "Category not found"], 404);
        }
        send_json($row);
    }

    // POST /categories
    public function create($data, $conn) {
        validate_admin_token($conn);

        $name = sanitize_string($data['category_name'] ?? '');
        $img  = sanitize_string($data['category_img'] ?? '');

        if ($name === '') send_json(["error" => "Category name required"], 400);
        if ($img === '')  send_json(["error" => "Category image link required"], 400);

        $id = $this->model->create($name, $img);
        if ($id !== false) {
            send_json(["success" => true, "category_id" => $id]);
        } else {
            send_json(["error" => "Failed to create category"], 500);
        }
    }

    // PATCH /categories/{id}
    public function update($id, $data, $conn) {
        validate_admin_token($conn);

        $id = validate_int($id);
        if ($id === false) send_json(["error" => "Invalid id"], 400);

        $name = sanitize_string($data['category_name'] ?? '');
        $img  = sanitize_string($data['category_img'] ?? '');

        if ($name === '') send_json(["error" => "Category name required"], 400);
        if ($img === '')  send_json(["error" => "Category image link required"], 400);

        $updated = $this->model->update($id, $name, $img);
        if ($updated) {
            send_json(["success" => true]);
        } else {
            send_json(["error" => "Category not found or no changes made"], 404);
        }
    }

    // DELETE /categories/{id}
    public function delete($id, $conn) {
        validate_admin_token($conn);

        $id = validate_int($id);
        if ($id === false) send_json(["error" => "Invalid id"], 400);

        $deleted = $this->model->delete($id);
        if ($deleted) {
            send_json(["success" => true]);
        } else {
            send_json(["error" => "Category not found"], 404);
        }
    }
}
?>
