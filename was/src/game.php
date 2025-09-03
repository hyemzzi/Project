<?php
session_start();
include 'db.php';

// GET으로 게임 제목(title) 받기
$game_title = $_GET['title'] ?? null;
$game = null;

if ($game_title) {
    $stmt = $conn->prepare("SELECT * FROM games WHERE title = ?");
    $stmt->bind_param("s", $game_title);
    $stmt->execute();
    $result = $stmt->get_result();
    $game = $result->fetch_assoc();

    // ✅ rule_images 컬럼 JSON → 배열 변환
    if (!empty($game['rule_images'])) {
        $rule_images = json_decode($game['rule_images'], true);
    } else {
        $rule_images = [];
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title><?php echo $game ? htmlspecialchars($game['title']) : "게임 없음"; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Noto+Sans+KR:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { margin:0; font-family:'Noto Sans KR',sans-serif; background:#f5f1e6; color:#2e1f0f;}
    header{position:sticky;top:0;display:flex;justify-content:space-between;align-items:center;
      padding:12px 50px;background:rgba(46,31,15,0.9);color:#f5f1e6;z-index:10;}
    header img{height:55px;}
    main{max-width:1000px;margin:40px auto;padding:20px;}
    h1{font-family:'Playfair Display',serif;font-size:2rem;margin-bottom:20px;}
    .game-info{display:flex;flex-wrap:wrap;gap:20px;align-items:flex-start;}
    .game-info img{width:350px;height:auto;border-radius:12px;box-shadow:0 6px 15px rgba(0,0,0,0.15);}
    .details{flex:1;min-width:300px;}
    .details p{margin:8px 0;font-size:1rem;}
    .btn-group{margin:30px 0;display:flex;gap:15px;}
    .btn{padding:12px 25px;font-size:1rem;border:none;border-radius:8px;cursor:pointer;
      font-weight:600;background:#2e1f0f;color:#f5f1e6;transition:all 0.3s;}
    .btn:hover{background:#c9a34f;color:#2e1f0f;}
    .content{margin-top:20px;}
    .scroll-box { max-height:0; overflow:hidden; transition:max-height 1s ease-in-out,padding 0.5s; padding:0;}
    .scroll-box.open { max-height:none; padding:20px 0;}
    .scroll-box img { width:100%; margin-bottom:20px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
    footer{text-align:center;padding:25px;font-size:0.9rem;color:#c9a34f;
      font-family:'Playfair Display',serif;background:#2e1f0f;margin-top:40px;}
  </style>
</head>
<body>
<header>
  <a href="index.php"><img src="https://images-dbtest.s3.ap-northeast-2.amazonaws.com/Lavel.png" alt="SOL드아웃 라벨"></a>
  <nav><?= htmlspecialchars($game['title'] ?? '게임 상세보기') ?></nav>
</header>
<main>
<?php if (!empty($game)): ?>
  <h1><?= htmlspecialchars($game['title']) ?></h1>
  <div class="game-info">
    <img src="<?= htmlspecialchars($game['image_url']) ?>" alt="<?= htmlspecialchars($game['title']) ?>">
    <div class="details">
      <p><strong>플레이 인원:</strong> <?= htmlspecialchars($game['players_min']) ?> ~ <?= htmlspecialchars($game['players_max']) ?>명</p>
      <p><strong>추천 인원:</strong> 베스트 <?= htmlspecialchars($game['best_players']) ?>명 / 추천 <?= htmlspecialchars($game['recommended_players']) ?>명</p>
      <p><strong>플레이 시간:</strong> <?= htmlspecialchars($game['playtime']) ?></p>
      <p><strong>난이도:</strong> ★ <?= htmlspecialchars($game['difficulty']) ?></p>
      <p><strong>연령 제한:</strong> <?= htmlspecialchars($game['age_limit']) ?>세 이상</p>
    </div>
  </div>

  <div class="btn-group">
    <button class="btn" onclick="showSection('images')">게임 설명</button>
    <button class="btn" onclick="showSection('video')">동영상</button>
  </div>

  <div id="images" class="content scroll-box">
    <?php
      if (!empty($game['rule_images'])):
          $rule_images = explode(',', $game['rule_images']);
          foreach ($rule_images as $img): ?>
            <img src="<?= htmlspecialchars(trim($img)) ?>"
                 alt="<?= htmlspecialchars($game['title']) ?> 설명 이미지">
    <?php endforeach;
      else: ?>
        <p>룰 이미지가 등록되어 있지 않습니다.</p>
    <?php endif; ?>
  </div>

  <div id="video" class="content scroll-box">
    <iframe width="800" height="450" 
            src="<?= htmlspecialchars($game['video_url']) ?>" 
            frameborder="0" allowfullscreen></iframe>
  </div>

<?php else: ?>
  <p style="text-align:center;">게임 정보를 불러올 수 없습니다.</p>
<?php endif; ?>
</main>
<footer>
  © 2025 보드게임 SOL드아웃. All Rights Reserved.
</footer>

<script>
function showSection(id) {
  document.querySelectorAll('.scroll-box').forEach(box => {
    box.classList.remove('open');
  });
  document.getElementById(id).classList.add('open');
}
</script>
</body>
</html>
