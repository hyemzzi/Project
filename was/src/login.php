<?php
session_start();
include 'db.php'; // DB 연결 (mysqli 객체 $conn 제공)

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM members WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "아이디 또는 비밀번호가 올바르지 않습니다.";
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>로그인 - 보드게임 SOL드아웃</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Noto+Sans+KR:wght@400;600&display=swap" rel="stylesheet">
<style>
  body {
    margin:0;
    font-family:'Noto Sans KR',sans-serif;
    background:#f5f1e6;
    color:#2e1f0f;
    display:flex;
    flex-direction:column;
    align-items:center;
  }
  header {
    width:100%;
    background:rgba(46,31,15,0.9);
    backdrop-filter:blur(6px);
    box-shadow:0 2px 6px rgba(0,0,0,0.15);
    padding:20px 0;
    text-align:center;
  }
  header img { height:60px; }

  .login-container {
    background:#fafafa;
    padding:40px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,0.2);
    margin-top:60px;
    width:90%;
    max-width:400px;
  }
  .login-container h2 {
    font-family:'Playfair Display',serif;
    font-weight:600;
    text-align:center;
    margin-bottom:30px;
    color:#2e1f0f;
  }
  .login-container form { display:flex; flex-direction:column; gap:20px; }
  .login-container input {
    padding:12px;
    border-radius:8px;
    border:1px solid #ddd;
    font-size:1rem;
    outline:none;
    transition:0.3s;
  }
  .login-container input:focus {
    border-color:#c9a34f;
    box-shadow:0 0 5px rgba(201,163,79,0.5);
  }
  .login-container button {
    padding:12px;
    background:#2e1f0f;
    color:#f5f1e6;
    font-family:'Playfair Display',serif;
    font-size:1.1rem;
    border:none;
    border-radius:8px;
    cursor:pointer;
    transition:0.3s;
  }
  .login-container button:hover {
    background:#c9a34f;
    color:#2e1f0f;
  }
  .login-container .error {
    color:#e74c3c;
    text-align:center;
    font-weight:bold;
  }
  .login-container .links {
    margin-top:15px;
    text-align:center;
  }
  .login-container .links a {
    color:#2e1f0f;
    text-decoration:none;
    margin:0 10px;
    font-weight:600;
  }
  .login-container .links a:hover { color:#c9a34f; }
</style>
</head>
<body>

<header>
  <a href="index.php"><img src="https://images-dbtest.s3.ap-northeast-2.amazonaws.com/Lavel.png" alt="SOL드아웃 라벨"></a>
</header>

<div class="login-container">
  <h2>로그인</h2>
  <form method="POST">
    <input type="text" name="username" placeholder="아이디" required>
    <input type="password" name="password" placeholder="비밀번호" required>
    <button type="submit">로그인</button>
  </form>
  <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
  <div class="links">
    <a href="register.php">회원가입</a> | 
    <a href="index.php">메인으로</a> <br><br>
    <a href="find_id.php">아이디 찾기</a> | 
    <a href="find_password.php">비밀번호 찾기</a>
  </div>
</div>

</body>
</html>

