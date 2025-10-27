<?php
// firebase/user_firebase.php
require_once __DIR__ . '/../config/db_connect.php';
require_once 'user-get-access-token.php';

// ✅ PHP < 8 fallback
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

/**
 * 🔔 Send FCM data-only notification for a specific user or all users
 *
 * @param mysqli $conn
 * @param int|null $user_id Specific user_id, null for all users
 * @param string $title Notification title
 * @param string $body Notification body
 * @param string $targetActivity "Main" বা "Message" (default: Message)
 */
function sendPushNotification($conn, $user_id = null, $title, $body, $targetActivity = 'Message') {
    if (!$body) return;

    // 🔐 Firebase Access Token
    $accessToken = getAccessToken(__DIR__ . '/user-service-account-file.json');
    if (!$accessToken) return;

    // -----------------------------
    // 1. Fetch tokens
    // -----------------------------
    if ($user_id === null) {
        // All users
        $sql = "SELECT id, fcm_token FROM user_fcm_tokens WHERE fcm_token IS NOT NULL";
        $stmt = $conn->prepare($sql);
    } else {
        // Specific user
        $sql = "SELECT id, fcm_token FROM user_fcm_tokens WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $tokens = [];
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['fcm_token'])) {
            $tokens[$row['id']] = $row['fcm_token'];
        }
    }

    if (empty($tokens)) return;

    // -----------------------------
    // 2. Send FCM data-only message
    // -----------------------------
    $url = "https://fcm.googleapis.com/v1/projects/walinker-a9214/messages:send";
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ];

    foreach ($tokens as $id => $token) {
        $message = [
            'message' => [
                'token' => $token,
                'data' => [
                    'title' => $title,
                    'body'  => $body,
                    'extra_info' => 'Custom data',
                    'target_activity' => $targetActivity
                ],
                'android' => ['priority' => 'HIGH']
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
        // 3. Handle invalid token
        // -----------------------------
        $resData = json_decode($response, true);
        if (isset($resData['error']) && str_contains($resData['error']['message'] ?? '', 'not a valid FCM registration token')) {
            $conn->query("DELETE FROM user_fcm_tokens WHERE id=" . intval($id));
        } else {
            $conn->query("UPDATE user_fcm_tokens SET updated_at = NOW() WHERE id=" . intval($id));
        }
    }
}

/**
 * 📨 Send SMS / Message notification
 * Can be used for send_sms table or group approval
 *
 * @param mysqli $conn
 * @param array $data ['user_id'=>?, 'sms'=>?]
 */
function onSendSmsPost($conn, $data) {
    $user_id = $data['user_id'] ?? null;
    $sms     = $data['sms'] ?? null;
    if (!$sms) return;

    sendPushNotification($conn, $user_id, "Message", $sms, 'Message');
}

/**
 * 🔔 Send notification to a group owner when their group is approved
 *
 * @param mysqli $conn
 * @param int $group_id
 * @param string $group_name
 */
function notifyGroupApproved($conn, $group_id, $group_name) {
    // Fetch group owner
    $stmt = $conn->prepare("SELECT user_id FROM `group` WHERE group_id=?");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $user_id = $row['user_id'];
        if ($user_id) {
            sendPushNotification($conn, $user_id, "Group Approved ✅", "Your group '{$group_name}' has been approved!", 'Main');
        }
    }
}
?>