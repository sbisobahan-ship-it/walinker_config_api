<?php
// firebase/firebase.php
require_once __DIR__ . '/../config/db_connect.php';
require_once 'get-access-token.php';

// ✅ Add this fallback for PHP < 8.0
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

/**
 * Send FCM notification (data-only) to all tokens in admin_tokens
 * Auto-delete invalid tokens
 * Auto-delete tokens older than 7 days
 */
function sendFCMNotification($title, $body, $conn) {
    // -----------------------------
    // 1. Delete old tokens (>7 days)
    // -----------------------------
    $conn->query("DELETE FROM admin_tokens WHERE updated_at < NOW() - INTERVAL 7 DAY");

    // -----------------------------
    // 2. Fetch tokens
    // -----------------------------
    $tokens = [];
    $sql = "SELECT id, fcm_token FROM admin_tokens";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tokens[$row['id']] = $row['fcm_token'];
        }
    }

    if (empty($tokens)) return;

    // -----------------------------
    // 3. FCM request setup
    // -----------------------------
    $accessToken = getAccessToken(__DIR__ . '/service-account-file.json');
    $url = "https://fcm.googleapis.com/v1/projects/walinker-a9214/messages:send";
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ];

    // -----------------------------
    // 4. Loop through tokens
    // -----------------------------
    foreach ($tokens as $id => $token) {

        $message = [
            'message' => [
                'token' => $token,
                // ✅ Notification (optional for foreground)
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                // ✅ Data payload (for deep link)
                'data' => [
                    'destination' => 'group_panding',
                    'extra_info' => 'Group push notification'
                ],
                'android' => [
                    'priority' => 'HIGH',
                    'notification' => [
                        'channel_id' => 'default_channel'
                        // click_action optional, deep link handled by data
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        // -----------------------------
        // 5. Handle invalid token
        // -----------------------------
        $resData = json_decode($response, true);
        if (isset($resData['error']) && str_contains($resData['error']['message'] ?? '', 'not a valid FCM registration token')) {
            $conn->query("DELETE FROM admin_tokens WHERE id=" . intval($id));
        } else {
            // valid token → update timestamp
            $conn->query("UPDATE admin_tokens SET updated_at=NOW() WHERE id=" . intval($id));
        }
    }
}
