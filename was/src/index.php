<?php
session_start();
include 'db.php';

$games = [];
$sql = "SELECT title, image_url FROM games"; 
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $games[] = $row;
    }
}

// 로그인 시 즐겨찾기 정보 가져오기
$userFavorites = [];
if(isset($_SESSION['username'])){
    $stmt = $conn->prepare("SELECT game_title FROM favorites WHERE username=?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $favResult = $stmt->get_result();
    while($row = $favResult->fetch_assoc()){
        $userFavorites[] = $row['game_title'];
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>보드게임 SOL드아웃</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Noto+Sans+KR:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { margin:0; font-family:'Noto Sans KR',sans-serif; background:#f5f1e6; color:#2e1f0f; overflow-x:hidden; }
    header { position:sticky; top:0; display:flex; justify-content:space-between; align-items:center; padding:12px 50px; background:rgba(46,31,15,0.9); backdrop-filter:blur(6px); box-shadow:0 2px 6px rgba(0,0,0,0.15); z-index:10; }
    .logo-link img { height:55px; }
    nav { display:flex; gap:30px; }
    nav a { text-decoration:none; font-family:'Playfair Display',serif; font-size:1.1rem; color:#f5f1e6; transition:color 0.3s; }
    nav a:hover { color:#c9a34f; }
    .container { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; max-width:1200px; margin:60px auto; }

    /* 카드 디자인 입체적 + 게임 이름 배경 */
    .photo-box {
      display:block;
      text-align:center;
      border-radius:12px;
      background:#fafafa;
      position:relative;
      overflow:hidden;
      cursor:pointer;
      transition: transform 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15), 0 8px 16px rgba(0,0,0,0.1);
    }
    .photo-box:hover {
      transform: translateY(-8px) scale(1.03);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2), 0 12px 24px rgba(0,0,0,0.15);
    }
    .photo-box img {
      width:100%;
      display:block;
      border-radius:12px;
    }
    .photo-title {
      position: absolute;
      bottom:0;
      width:100%;
      padding:8px 12px;
      background: rgba(46,31,15,0.7);
      color:#fff;
      font-weight:bold;
      font-size:1.1rem;
      text-align:center;
      box-sizing:border-box;
    }

    footer { text-align:center; padding:25px; font-size:0.9rem; color:#c9a34f; font-family:'Playfair Display',serif; background:#2e1f0f; }

    /* 즐겨찾기 버튼 */
    .fav-btn { position:absolute; top:10px; right:10px; font-size:1.6rem; cursor:pointer; color:#ccc; transition: transform 0.2s, color 0.3s; }
    .fav-btn:hover { transform: scale(1.2); }
    .fav-btn.fav { color:#e74c3c; }

    /* 메인, 헤더, 푸터 페이드인 */
    main, header, footer { opacity:0; transition:opacity 1s ease; }
    body.show-main main, body.show-main header, body.show-main footer { opacity:1; }

    /* 카드 날리는 효과 */
    .card { position:fixed; top:50%; left:50%; width:80px; height:120px; background:white; border:2px solid #2e1f0f; border-radius:8px; box-shadow:0 4px 15px rgba(0,0,0,0.3); display:flex; justify-content:center; align-items:center; font-family:'Playfair Display',serif; font-size:1.5rem; transform:translate(-50%,-50%) rotate(0deg); opacity:0; z-index:9999; animation:fly 2s forwards; }
    @keyframes fly { 0% {opacity:1; transform:translate(-50%,-50%) rotate(0deg) scale(1);} 100% {opacity:0; transform:translate(calc(-50% + (var(--x))), calc(-50% + (var(--y)))) rotate(var(--r)) scale(0.8);} }
  </style>
</head>
<body>

<!-- 🎴 카드 컨테이너 -->
<div id="cards"></div>

<header>
  <a href="index.php" class="logo-link">
    <img src="https://images-dbtest.s3.ap-northeast-2.amazonaws.com/Lavel.png" alt="SOL드아웃 라벨">
  </a>
  <nav>
    <?php if (isset($_SESSION['username'])): ?>
        <span style="color:#f5f1e6;">안녕하세요, <?= htmlspecialchars($_SESSION['username']); ?>님</span>
        <a href="logout.php">로그아웃</a>
        <a href="favorites.php">즐겨찾기</a>
    <?php else: ?>
        <a href="login.php">로그인</a>
        <a href="register.php">회원가입</a>
    <?php endif; ?>
  </nav>
</header>

<main>
  <div class="container">
    <?php if (!empty($games)): ?>
      <?php foreach ($games as $game): ?>
        <?php $isFav = in_array($game['title'], $userFavorites); ?>
        <div class="photo-box" data-title="<?= htmlspecialchars($game['title']) ?>">
          <span class="fav-btn <?= $isFav ? 'fav' : '' ?>">★</span>
          <a href="game.php?title=<?= urlencode($game['title']) ?>">
            <img src="<?= htmlspecialchars($game['image_url']) ?>" alt="<?= htmlspecialchars($game['title']) ?>">
            <div class="photo-title"><?= htmlspecialchars($game['title']) ?></div>
          </a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="text-align:center;">게임 정보를 불러올 수 없습니다.</p>
    <?php endif; ?>
  </div>  
</main>

<footer>
  © 2025 보드게임 SOL드아웃. All Rights Reserved.
</footer>

<div style="position:fixed;right:20px;bottom:20px;z-index:100;">
  <img src="https://images-dbtest.s3.ap-northeast-2.amazonaws.com/logo.png" alt="로고" style="height:65px;">
</div>

<script>
<?php if (!isset($_SESSION['username'])): ?>
  // 🎴 카드 날리는 효과
  const suits = ["♠","♥","♦","♣"];
  const ranks = ["A","K","Q","J","10","9"];
  const cardsContainer = document.getElementById("cards");

  for(let i=0;i<15;i++){
    const card = document.createElement("div");
    card.className = "card";
    const suit = suits[Math.floor(Math.random()*suits.length)];
    const rank = ranks[Math.floor(Math.random()*ranks.length)];
    card.textContent = rank+suit;
    if(suit==="♥"||suit==="♦") card.style.color="red";
    card.style.setProperty("--x",(Math.random()*600-300)+"px");
    card.style.setProperty("--y",(Math.random()*400-200)+"px");
    card.style.setProperty("--r",(Math.random()*720-360)+"deg");
    card.style.animationDelay=(i*0.1)+"s";
    cardsContainer.appendChild(card);
  }
  setTimeout(()=>{
    document.body.classList.add("show-main");
    cardsContainer.remove();
  },3500);
<?php else: ?>
  // 로그인 상태에서는 바로 메인 보여주기
  document.body.classList.add("show-main");
<?php endif; ?>

  // ⭐ 즐겨찾기 클릭 이벤트
  document.querySelectorAll('.fav-btn').forEach(btn=>{
    btn.addEventListener('click',function(e){
      e.preventDefault();
      const box = this.closest('.photo-box');
      const title = box.getAttribute('data-title');
      fetch('favorite_toggle.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'game_title='+encodeURIComponent(title)
      })
      .then(res=>res.json())
      .then(data=>{
        if(data.status==='added') this.classList.add('fav');
        else if(data.status==='removed') this.classList.remove('fav');
        else alert(data.message);
      })
      .catch(err=>console.error(err));
    });
  });
</script>

</body>
</html>

