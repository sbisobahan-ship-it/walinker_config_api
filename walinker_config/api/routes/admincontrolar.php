<?php
require_once __DIR__ . '/../controllers/adminController.php';

if (isset($route_parts[0]) && $route_parts[0] === 'admincontrolar') {
    handle_admin_request($route_parts, $conn);
    $handled = true;
}
?>
