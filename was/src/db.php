<?php
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');
$db   = getenv('DB_NAME');
#테스트입니다.
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
