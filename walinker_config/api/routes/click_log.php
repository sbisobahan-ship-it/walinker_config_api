<?php
require_once __DIR__ . '/../controllers/clickLogController.php';

if (isset($route_parts[0]) && $route_parts[0] === 'click_log') {
    handle_click_log_request($route_parts, $conn);
    $handled = true;
}
