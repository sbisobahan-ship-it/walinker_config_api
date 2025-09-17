<?php
if (isset($route_parts[0]) && $route_parts[0] === 'group') {
    require_once __DIR__ . '/../controllers/groupController.php';
    handle_group_request($route_parts, $conn);
}
