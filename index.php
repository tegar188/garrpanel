<?php
$game     = $_REQUEST['game']     ?? '';
$user_key = $_REQUEST['user_key'] ?? '';
$serial   = $_REQUEST['serial']   ?? '';

$host = getenv('MYSQLHOST');
$port = getenv('MYSQLPORT') ?: 3306;
$db   = getenv('MYSQLDATABASE');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
} catch (Exception $e) {
    echo "INVALID"; exit;
}

$stmt = $pdo->prepare('SELECT * FROM keys_table WHERE key_value = ? AND banned = 0 LIMIT 1');
$stmt->execute([$user_key]);
$row = $stmt->fetch();

if (!$row) { echo "INVALID"; exit; }
if ($row['expires_at'] && strtotime($row['expires_at']) < time()) { echo "INVALID"; exit; }
if ($row['hwid'] && $row['hwid'] !== $serial) { echo "INVALID"; exit; }
if (!$row['hwid']) {
    $pdo->prepare('UPDATE keys_table SET hwid=?, used=1, activated_at=NOW(), expires_at=DATE_ADD(NOW(), INTERVAL ? DAY) WHERE key_value=?')
       ->execute([$serial, $row['duration'], $user_key]);
}
echo "OK";
