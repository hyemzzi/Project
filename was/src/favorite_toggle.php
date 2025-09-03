<?php
session_start();
include 'db.php';

if(!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => '로그인이 필요합니다.']);
    exit;
}

if(isset($_POST['game_title'])) {
    $username = $_SESSION['username'];
    $game_title = $_POST['game_title'];

    // 이미 즐겨찾기 되어 있는지 체크
    $stmt = $conn->prepare("SELECT * FROM favorites WHERE username=? AND game_title=?");
    $stmt->bind_param("ss", $username, $game_title);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        // 삭제
        $del = $conn->prepare("DELETE FROM favorites WHERE username=? AND game_title=?");
        $del->bind_param("ss", $username, $game_title);
        $del->execute();
        echo json_encode(['status'=>'removed']);
    } else {
        // 추가
        $ins = $conn->prepare("INSERT INTO favorites (username, game_title) VALUES (?, ?)");
        $ins->bind_param("ss", $username, $game_title);
        $ins->execute();
        echo json_encode(['status'=>'added']);
    }
}
?>

