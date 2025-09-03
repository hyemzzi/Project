<?php
session_start();
include 'db.php'; // DB 연결 ($conn)

// 👇 DB 연결 직후 UTF-8 설정
$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $name     = $_POST['name'];
    $password = $_POST['password'];

    // 동일 아이디 체크
    $check = $conn->prepare("SELECT * FROM members WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "이미 존재하는 아이디입니다.";
    } else {
        // INSERT 실행
        $stmt = $conn->prepare("INSERT INTO members (username, name, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $name, $password);
        
        if ($stmt->execute()) {
            // 성공 시 로그인 페이지로 이동
            header("Location: login.php");
            exit;
        } else {
            $error = "회원가입 실패: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>회원가입 - 보드게임 SOL드아웃</title>
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

  /* 상단 라벨 */
  header {
    width:100%;
    background:rgba(46,31,15,0.9);
    backdrop-filter:blur(6px);
    box-shadow:0 2px 6px rgba(0,0,0,0.15);
    padding:20px 0;
    text-align:center;
  }
  header img {
    height:60px;
  }

  /* 회원가입 폼 컨테이너 */
  .register-container {
    background:#fafafa;
    padding:40px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,0.2);
    margin-top:60px;
    width:90%;
    max-width:400px;
  }

  .register-container h2 {
    font-family:'Playfair Display',serif;
    font-weight:600;
    text-align:center;
    margin-bottom:30px;
    color:#2e1f0f;
  }

  .register-container form {
    display:flex;
    flex-direction:column;
    gap:20px;
  }

  .register-container input {
    padding:12px;
    border-radius:8px;
    border:1px solid #ddd;
    font-size:1rem;
    outline:none;
    transition:0.3s;
  }
  .register-container input:focus {
    border-color:#c9a34f;
    box-shadow:0 0 5px rgba(201,163,79,0.5);
  }

  .register-container button {
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
  .register-container button:hover {
    background:#c9a34f;
    color:#2e1f0f;
  }

  .register-container .error {
    color:#e74c3c;
    text-align:center;
    font-weight:bold;
  }

  .register-container .links {
    margin-top:15px;
    text-align:center;
  }
  .register-container .links a {
    color:#2e1f0f;
    text-decoration:none;
    margin:0 10px;
    font-weight:600;
  }
  .register-container .links a:hover {
    color:#c9a34f;
  }
</style>
</head>
<body>

<header>
  <a href="index.php"><img src="https://images-dbtest.s3.ap-northeast-2.amazonaws.com/Lavel.png" alt="SOL드아웃 라벨"></a>
</header>

<div class="register-container">
  <h2>회원가입</h2>
  <form method="POST">
    <input type="text" name="username" placeholder="아이디" required>
    <input type="text" name="name" placeholder="이름" required>
    <input type="password" name="password" placeholder="비밀번호" required>
    <button type="submit">회원가입</button>
  </form>
  <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
  <div class="links">
    <a href="login.php">로그인</a> | 
    <a href="index.php">메인으로</a>
  </div>
</div>

</body>
</html>

