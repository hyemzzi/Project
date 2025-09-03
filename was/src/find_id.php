<?php
session_start();
include 'db.php';
$conn->set_charset("utf8mb4");

$success = $error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name  = $_POST['name'];

    $stmt = $conn->prepare("SELECT username FROM members WHERE name=?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $success = "회원님의 아이디는: <strong>{$row['username']}</strong> 입니다.";
    } else {
        $error = "정보가 일치하지 않습니다.";
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>아이디 찾기 - 보드게임 SOL드아웃</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Noto+Sans+KR:wght@400;600&display=swap" rel="stylesheet">
<style>
body { margin:0; font-family:'Noto Sans KR',sans-serif; background:#f5f1e6; color:#2e1f0f; display:flex; flex-direction:column; align-items:center; }
header { width:100%; background:rgba(46,31,15,0.9); backdrop-filter:blur(6px); box-shadow:0 2px 6px rgba(0,0,0,0.15); padding:20px 0; text-align:center; }
header img { height:60px; cursor:pointer; }
.container { background:#fafafa; padding:40px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.2); margin-top:60px; width:90%; max-width:400px; text-align:center; }
input, button { padding:12px; margin:10px 0; width:80%; border-radius:8px; border:1px solid #ddd; }
button { background:#2e1f0f; color:#f5f1e6; border:none; cursor:pointer; }
button:hover { background:#c9a34f; color:#2e1f0f; }
.error { color:#e74c3c; font-weight:bold; }
.success { color:#2e1f0f; font-weight:bold; }
</style>
</head>
<body>

<header>
  <a href="index.php"><img src="https://images-dbtest.s3.ap-northeast-2.amazonaws.com/Lavel.png" alt="SOL드아웃 라벨"></a>
</header>

<div class="container">
  <h2>아이디 찾기</h2>
  <form method="POST">
    <input type="text" name="name" placeholder="이름 입력" required>
    <button type="submit">아이디 확인</button>
  </form>
  <?php
  if ($success) echo "<p class='success'>$success</p>";
  if ($error) echo "<p class='error'>$error</p>";
  ?>
</div>

</body>
</html>

