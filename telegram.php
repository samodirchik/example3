<?php
// ==========================================
// TELEGRAM BOT SETTINGS
// ==========================================
$token = "";
$chat_id = "";

// Allow CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $phone = isset($_POST['phone']) ? htmlspecialchars(strip_tags(trim($_POST['phone']))) : '';
    $name = isset($_POST['name']) ? htmlspecialchars(strip_tags(trim($_POST['name']))) : '';

    if (empty($phone)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Phone number is required.']);
        exit;
    }

    $txt = "🚀 <b>New request from Konderich website!</b>\n\n";
    if (!empty($name)) {
        $txt .= "👤 <b>Name:</b> " . $name . "\n";
    }
    $txt .= "📞 <b>Phone:</b> " . $phone . "\n";

    $url_api = "https://api.telegram.org/bot{$token}/sendMessage";

    $data = array(
        'chat_id' => $chat_id,
        'text' => $txt,
        'parse_mode' => 'HTML'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_api);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $result = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($http_code == 200) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Request successfully sent']);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to send to Telegram',
            'telegram_error' => $error,
            'result' => $result
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
}
?>