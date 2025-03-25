<?php
// 한글 인코딩 설정
header('Content-Type: text/html; charset=UTF-8');
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');

// DB 연결 파일 포함
require_once 'db_connect.php';

// 폼 제출 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 폼 데이터 받기
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $pass = trim($_POST['pass'] ?? '');
    
    // 필수 필드 검증
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "제목을 입력해주세요.";
    }
    
    if (empty($content)) {
        $errors[] = "내용을 입력해주세요.";
    }
    
    if (empty($name)) {
        $errors[] = "작성자 이름을 입력해주세요.";
    }
    
    if (empty($pass)) {
        $errors[] = "비밀번호를 입력해주세요.";
    }
    
    // 오류가 없으면 저장
    if (empty($errors)) {
        // DB 연결
        $db = get_db_connection();
        
        // 오늘 날짜
        $today = date("Y.m.d");
        
        // SQL 삽입
        $title_escaped = $db->conn->real_escape_string($title);
        $content_escaped = $db->conn->real_escape_string($content);
        $name_escaped = $db->conn->real_escape_string($name);
        
        $sql = "INSERT INTO board(title, name, pass, content, day) 
                VALUES('$title_escaped', '$name_escaped', '$pass', '$content_escaped', '$today')";
                
        $result = $db->sql($sql);
        
        // DB 연결 종료
        $db->DB_close();
        
        // 성공 시 목록 페이지로 이동
        if ($result) {
            header("Location: list.php");
            exit;
        } else {
            $errors[] = "게시물 저장 중 오류가 발생했습니다.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글쓰기</title>
    <?php echo get_common_css(); ?>
</head>
<body>
    <div class="container">
        <h1>글쓰기</h1>
        
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
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="name">작성자</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="pass">비밀번호</label>
                <input type="password" id="pass" name="pass" required>
            </div>
            
            <div class="form-group">
                <label for="content">내용</label>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($content ?? ''); ?></textarea>
            </div>
            
            <div class="buttons">
                <button type="submit" class="btn">저장하기</button>
                <button type="button" class="btn btn-cancel" onclick="location.href='list.php'">취소하기</button>
            </div>
        </form>
    </div>
</body>
</html>