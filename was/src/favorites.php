<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// 즐겨찾기 목록 조회
$games = [];
$sql = "SELECT g.title, g.image_url
        FROM games g
        JOIN favorites f ON g.title = f.game_title
	WHERE f.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $games[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>즐겨찾기 - SOL드아웃</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Noto+Sans+KR:wght@400;600&display=swap" rel="stylesheet">
<style>
body { margin:0; font-family: 'Noto Sans KR', sans-serif; background:#f5f1e6; color:#2e1f0f; }
header, footer { background:#2e1f0f; color:#f5f1e6; padding:12px 50px; position:sticky; top:0; display:flex; align-items:center; justify-content:space-between; }
header nav { display:flex; align-items:center; gap:20px; }
.container { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:40px; padding:60px 80px; max-width:1400px; margin:0 auto; justify-content:center; }
.photo-box { width:260px; height:360px; border-radius:12px; overflow:hidden; background:#fff; box-shadow:0 6px 15px rgba(0,0,0,0.15); text-align:center; position:relative; cursor:pointer; transition: transform 0.3s; display:flex; flex-direction:column; margin:0 auto; }
.photo-box:hover { transform: translateY(-8px); }
.photo-box img { width:100%; height:240px; object-fit:cover; background:#fff; display:block; } /* index.php와 동일 크기 */
.photo-title { font-family:'Playfair Display', serif; font-size:1.2rem; padding:10px; background:#2e1f0f; height:auto; display:flex; align-items:center; justify-content:center; text-align:center; color:#f5f1e6; }
.favorite-btn { position:absolute; top:10px; right:10px; font-size:1.5rem; color:red; cursor:pointer; user-select:none; }
</style>
</head>
<body>

<header>
<a href="index.php"><img src="https://images-dbtest.s3.ap-northeast-2.amazonaws.com/Lavel.png" alt="SOL드아웃 라벨" style="height:55px;"></a>
<nav>
<span>안녕하세요, <?= htmlspecialchars($username) ?>님</span>
<a href="logout.php">로그아웃</a>
<a href="index.php">게임목록</a>
</nav>
</header>

<main>
<div class="container">
<?php if (!empty($games)): ?>
    <?php foreach ($games as $game): ?>
    <div class="photo-box" data-title="<?= htmlspecialchars($game['title']) ?>">
        <a href="game.php?title=<?= urlencode($game['title']) ?>">
            <img src="<?= htmlspecialchars($game['image_url']) ?>" alt="<?= htmlspecialchars($game['title']) ?>">
        </a>
        <div class="photo-title"><?= htmlspecialchars($game['title']) ?></div>
        <div class="favorite-btn">★</div>
    </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="text-align:center;">즐겨찾기한 게임이 없습니다.</p>
<?php endif; ?>
</div>
</main>

<footer>
© 2025 보드게임 SOL드아웃. All Rights Reserved.
</footer>

<script>
// 즐겨찾기 클릭 시 즉시 제거 + 서버 요청 백그라운드
document.querySelectorAll('.favorite-btn').forEach(btn=>{
    btn.addEventListener('click', (e)=>{
        e.stopPropagation(); // 카드 클릭 이벤트 방지
        const box = btn.closest('.photo-box');
        box.remove(); // 클릭 즉시 카드 제거

        const title = box.getAttribute('data-title');
        fetch('favorite_toggle.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'game_title=' + encodeURIComponent(title)
        });
    });
});

// 카드 클릭 시 링크 이동
document.querySelectorAll('.photo-box').forEach(box=>{
    box.addEventListener('click', ()=>{
        const link = box.querySelector('a');
        if(link) window.location.href = link.href;
    });
});
</script>

</body>
</html>

