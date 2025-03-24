<!-- 글 삭제 DB 처리 페이지 -->
<?php
// 데이터베이스 클래스 포함
require_once 'db_connect.php';

// 데이터베이스 연결 객체 얻기
$db = DBConnection::getInstance();
$conn = $db->getConnection();

$num = $_GET["num"];

$sql = "DELETE FROM board WHERE num=$num;";
$result = $db->query($sql);

// 데이터베이스 연결 종료
$db->close();
?>

<!-- 글 삭제 화면 구성 -->
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글 삭제 결과</title>
    <?php echo get_common_css(); ?>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .result-container {
            max-width: 500px;
            width: 100%;
            padding: 30px;
            text-align: center;
        }
        .success-message {
            color: #4CAF50;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .error-message {
            color: #f44336;
            font-size: 24px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container result-container">
        <?php if($result): ?>
            <div class="success-message">글이 성공적으로 삭제되었습니다!</div>
            <p>게시글이 데이터베이스에서 완전히 삭제되었습니다.</p>
        <?php else: ?>
            <div class="error-message">글 삭제에 실패했습니다.</div>
            <p>오류 메시지: <?php echo $db->error(); ?></p>
        <?php endif; ?>
        <div style="margin-top: 20px;">
            <button type="button" onclick="location.href='board_list.php'" class="button">글 목록</button>
        </div>
    </div>
</body>
</html>