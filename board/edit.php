<?php
// DB 연결 파일 포함
require_once 'db_connect.php';

// 게시물 번호 검증
$num = isset($_GET['num']) ? (int)$_GET['num'] : 0;

// 게시물 번호가 없거나 0보다 작으면 목록 페이지로 이동
if ($num <= 0) {
    header("Location: list.php");
    exit;
}

// DB 연결
$db = get_db_connection();

// 폼 제출 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $pass = trim($_POST['pass'] ?? '');
    
    // 필수 필드 검증
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "제목을 입력해주세요.";
    }
    
    if (empty($content)) {
        $errors[] = "내용을 입력해주세요.";
    }
    
    if (empty($pass)) {
        $errors[] = "비밀번호를 입력해주세요.";
    }
    
    $db->sql("SELECT pass FROM board WHERE num = $num");
    $db->fetch_row();
    $stored_password = $db->f('pass');
    
    // 비밀번호 검증
    if ($pass !== $stored_password) {
        $errors[] = "비밀번호가 일치하지 않습니다.";
    }
    
    // 오류가 없으면 업데이트
    if (empty($errors)) {
        $today = date("Y.m.d");
        $title_escaped = htmlspecialchars($title);
        $content_escaped = htmlspecialchars($content);
        
        $sql = "UPDATE board SET 
                title = '$title_escaped', 
                content = '$content_escaped',
                day = '$today'
                WHERE num = $num";
                
        $result = $db->sql($sql);
        
        $db->DB_close();
        
        if ($result) {
            // 수정 성공 시 게시물 보기 페이지로 이동
            header("Location: view.php?num=$num");
            exit;
        } else {
            // 수정 실패 시 오류 메시지 추가
            $errors[] = "게시물 수정 중 오류가 발생했습니다.";
        }
    }
} else {
    // 게시물 데이터 조회
    $post = $db->fetchArray("SELECT title, content, name FROM board WHERE num = $num");
    
    // 게시물이 존재하지 않으면 목록 페이지로 이동
    if (!$post) {
        $db->DB_close();
        header("Location: list.php");
        exit;
    }
    
    $title = $post['title'];
    $content = $post['content'];
    $name = $post['name'];
}
?>

<!-- 게시글 수정 페이지 화면 -->
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 수정</title>
    <?php echo get_common_css(); ?>
</head>
<body>
    <div class="container">
        <h1>게시글 수정</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="" accept-charset="UTF-8">
            <div class="form-group">
                <label for="title">제목</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="name">작성자</label>
                <input type="text" id="name" value="<?php echo htmlspecialchars($name); ?>" readonly>
            </div>
            
            <div class="form-group">
                <label for="pass">비밀번호 확인</label>
                <input type="password" id="pass" name="pass" required>
            </div>
            
            <div class="form-group">
                <label for="content">내용</label>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            
            <div class="buttons">
                <button type="submit" class="btn">수정하기</button>
                <button type="button" class="btn btn-cancel" onclick="location.href='view.php?num=<?php echo $num; ?>'">취소하기</button>
            </div>
        </form>
    </div>
</body>
</html>