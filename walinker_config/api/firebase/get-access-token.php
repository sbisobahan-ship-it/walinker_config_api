<?php

function getAccessToken($serviceAccountKeyFile)
{
    $cacheFile = __DIR__ . '/access_token_cache.json';

    // যদি cache ফাইল থাকে → আগের টোকেন ব্যবহার
    if (file_exists($cacheFile)) {
        $cache = json_decode(file_get_contents($cacheFile), true);
        if (isset($cache['access_token'], $cache['expires_at'])) {
            if (time() < $cache['expires_at']) {
                // এখনও মেয়াদ আছে → আগের টোকেন রিটার্ন করো
                return $cache['access_token'];
            }
        }
    }

    // নতুন JWT বানানো
    $serviceAccount = json_decode(file_get_contents($serviceAccountKeyFile), true);
    $jwt = generateJWT($serviceAccount);
    $url = 'https://oauth2.googleapis.com/token';

    $post = [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        die('Error obtaining access token');
    }

    $data = json_decode($response, true);

    if (isset($data['access_token'], $data['expires_in'])) {
        // cache করে রাখা (বর্তমান সময় + expires_in)
        $cacheData = [
            'access_token' => $data['access_token'],
            'expires_at' => time() + $data['expires_in'] - 60 // ১ মিনিট কম রাখা buffer
        ];
        file_put_contents($cacheFile, json_encode($cacheData));

        return $data['access_token'];
    } else {
        die('Error obtaining access token');
    }
}

// Function to generate JWT
function generateJWT($serviceAccount)
{
    $header = [
        'alg' => 'RS256',
        'typ' => 'JWT',
    ];

    $now = time();
    $exp = $now + 3600; // 1 hour expiration

    $payload = [
        'iss' => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $exp,
    ];

    $base64UrlHeader = base64UrlEncode(json_encode($header));
    $base64UrlPayload = base64UrlEncode(json_encode($payload));

    $signatureInput = $base64UrlHeader . '.' . $base64UrlPayload;
    $signature = '';
    openssl_sign($signatureInput, $signature, $serviceAccount['private_key'], 'sha256');

    $base64UrlSignature = base64UrlEncode($signature);

    return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
}

function base64UrlEncode($data)
{
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

?>