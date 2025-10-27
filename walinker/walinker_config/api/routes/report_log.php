<?php
require_once __DIR__ . '/../controllers/reportLogController.php';

if (isset($route_parts[0]) && $route_parts[0] === 'report_log') {
    handle_report_log_request($route_parts, $conn);
    $handled = true;
}
