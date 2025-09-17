<?php
require_once __DIR__ . '/../controllers/group_infoController.php';

if (isset($route_parts[0]) && $route_parts[0] === 'group_info') {
    handle_group_info_request($route_parts, $conn);
    $handled = true;
}
