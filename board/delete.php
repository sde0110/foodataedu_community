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

// 비밀번호 확인 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = trim($_POST['pass'] ?? '');
    
    // 비밀번호 검증
    $db->sql("SELECT pass FROM board WHERE num = $num");
    $db->fetch_row();
    $stored_password = $db->f('pass');
    
    // 비밀번호가 일치하면 삭제
    if ($pass === $stored_password) {
        // 직접 SQL 삭제 쿼리 실행
        $sql = "DELETE FROM board WHERE num = $num";
        $result = $db->sql($sql);
        
        // DB 연결 종료
        $db->DB_close();
        
        // 성공 시 목록 페이지로 이동
        if ($result) {
            header("Location: list.php");
            exit;
        } else {
            $error = "게시물 삭제 중 오류가 발생했습니다.";
        }
    } else {
        $error = "비밀번호가 일치하지 않습니다.";
    }
} else {
    // 게시물이 존재하는지 확인
    $post = $db->fetchArray("SELECT title FROM board WHERE num = $num");
    
    if (!$post) {
        $db->DB_close();
        header("Location: list.php");
        exit;
    }
    
    $title = $post['title'];
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 삭제</title>
    <?php echo get_common_css(); ?>
</head>
<meta charset="UTF-8">
<body>
    <div class="container">
        <h1>게시글 삭제</h1>
        
        <p>"<?php echo htmlspecialchars($title ?? ''); ?>" 게시글을 삭제하시겠습니까?</p>
        <p>삭제하려면 글 작성 시 입력한 비밀번호를 입력하세요.</p>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        
        <form method="post" action="" accept-charset="UTF-8">
            <div class="form-group">
                <label for="pass">비밀번호</label>
                <input type="password" id="pass" name="pass" required>
            </div>
            
            <div class="buttons">
                <button type="submit" class="btn">삭제하기</button>
                <button type="button" class="btn btn-cancel" onclick="location.href='view.php?num=<?php echo $num; ?>'">취소하기</button>
            </div>
        </form>
    </div>
</body>
</html>