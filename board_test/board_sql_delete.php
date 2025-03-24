<!-- 글 삭제 DB 처리 페이지 -->
<?php
	$num = $_GET["num"];
	
	// 데이터베이스 클래스 포함
	require_once 'db_connect.php';

	// 데이터베이스 연결 객체 얻기
	$db = DBConnection::getInstance();
	$conn = $db->getConnection();

	$sql = "DELETE FROM board WHERE num=$num;";
	$db->query($sql);

	// 데이터베이스 연결 종료
	$db->close();
?>

<!-- 글 삭제 화면 구성 -->
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
    </head>
    <body>
        <div>
			<p>글을 삭제했습니다.</p>
        	<button type="button" onclick="location.href='board_list.php'">글목록</button>
		</div>
    </body>
</html>