<?php
require_once __DIR__ . '/../controllers/viewLogController.php';

if (isset($route_parts[0]) && $route_parts[0] === 'view_log') {
    handle_view_log_request($route_parts, $conn);
    $handled = true;
}
