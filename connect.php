<?php
// connect.php — handle /connect endpoint
header('Content-Type: application/json');

$key = $_POST['key'] ?? $_GET['key'] ?? '';
$hwid = $_POST['hwid'] ?? $_GET['hwid'] ?? '';

// reuse logic yang sama kayak verify.php
// atau redirect internal:
$_GET['key'] = $key;
$_GET['hwid'] = $hwid;
include 'verify.php';
