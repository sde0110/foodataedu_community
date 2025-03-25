<?php
// DB 연결 파일 포함
require_once 'db_connect.php';

// 게시물 번호 검증
$num = isset($_GET['num']) ? (int)$_GET['num'] : 0;

if ($num <= 0) {
    header("Location: list.php");
    exit;
}

// DB 연결
$db = get_db_connection();

// 게시물 조회
$post = $db->fetchArray("SELECT num, title, name, content, day FROM board WHERE num = $num");

// 게시물이 없으면 목록으로 리다이렉트
if (!$post) {
    $db->DB_close();
    header("Location: list.php");
    exit;
}

// 게시물 데이터
$title = $post['title'];
$content = $post['content'];
$name = $post['name'];
$day = $post['day'];

// DB 연결 종료
$db->DB_close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <?php echo get_common_css(); ?>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($title); ?></h1>
        
        <div class="post-info">
            <span>작성자: <?php echo htmlspecialchars($name); ?></span>
            <span>작성일: <?php echo $day; ?></span>
        </div>
        
        <div class="post-content">
            <?php echo nl2br(htmlspecialchars($content)); ?>
        </div>
        
        <div class="buttons">
            <a href="list.php" class="btn">목록</a>
            <a href="edit.php?num=<?php echo $num; ?>" class="btn btn-edit">수정</a>
            <a href="delete.php?num=<?php echo $num; ?>" class="btn btn-delete">삭제</a>
        </div>
    </div>
</body>
</html>