<?php
if (isset($route_parts[0]) && $route_parts[0] === 'users') {
    $handled = true;
    require_once __DIR__ . '/../controllers/usersController.php';
    handle_users_request($route_parts, $conn);
}
