<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$debug = [
    'host' => getenv('MYSQLHOST'),
    'db'   => getenv('MYSQLDATABASE'),
    'user' => getenv('MYSQLUSER'),
];
file_put_contents('/tmp/connect_debug.log', json_encode($_POST) . ' | ENV: ' . json_encode($debug) . "\n", FILE_APPEND);

$user_key = $_POST['user_key'] ?? '';
$serial   = $_POST['serial'] ?? '';
$game     = $_POST['game'] ?? '';

$host = getenv('MYSQLHOST');
$port = getenv('MYSQLPORT') ?: 3306;
$db   = getenv('MYSQLDATABASE');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'data' => 'DB_ERROR: ' . $e->getMessage()]); exit;
}

$stmt = $pdo->prepare('SELECT * FROM keys_table WHERE key_value = ? AND banned = 0 LIMIT 1');
$stmt->execute([$user_key]);
$row = $stmt->fetch();

if (!$row) {
    echo json_encode(['status' => 'error', 'data' => 'INVALID_KEY']); exit;
}
if ($row['expires_at'] && strtotime($row['expires_at']) < time()) {
    echo json_encode(['status' => 'error', 'data' => 'EXPIRED']); exit;
}
if ($row['hwid'] && $row['hwid'] !== $serial) {
    echo json_encode(['status' => 'error', 'data' => 'HWID_MISMATCH']); exit;
}
if (!$row['hwid']) {
    $pdo->prepare('UPDATE keys_table SET hwid=?, used=1, activated_at=NOW(), expires_at=DATE_ADD(NOW(), INTERVAL ? DAY) WHERE key_value=?')
       ->execute([$serial, $row['duration'], $user_key]);
}

echo json_encode(['status' => 'success', 'data' => 'OK']);
