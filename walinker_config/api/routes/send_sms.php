<?php
require_once __DIR__ . '/../controllers/sendSmsController.php';

if (isset($route_parts[0]) && $route_parts[0] === 'send_sms') {
	handle_send_sms_request($route_parts, $conn);
	$handled = true;
}

?>
