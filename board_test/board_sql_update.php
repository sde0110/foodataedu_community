<!-- 글 수정 DB 처리 페이지 -->
<?php
	$num = $_GET["num"];
	$name = htmlspecialchars($_POST["name"], ENT_QUOTES);
	$pw = htmlspecialchars($_POST["pw"], ENT_QUOTES);
	$title = htmlspecialchars($_POST["title"], ENT_QUOTES);
	$content = htmlspecialchars($_POST["content"], ENT_QUOTES);

	// 오늘 날짜 확인
	$today = date("Y.m.d");

	// 데이터베이스 클래스 포함
	require_once 'db_connect.php';

	// 데이터베이스 연결 객체 얻기
	$db = DBConnection::getInstance();
	$conn = $db->getConnection();

	$sql = "UPDATE board SET name='$name', pass='$pw', title='$title', content='$content', day='$today' WHERE num=$num";
	
	$result = $db->query($sql);

	if($result) {
		echo "<div style='text-align: center; margin-top: 20px;'>";
		echo "<h3 style='color: green;'>글 수정 성공</h3>";
		echo "<button type='button' onclick=\"location.href='board_list.php'\" 
              style='padding: 8px 15px; background-color: #4CAF50; color: white; 
              border: none; border-radius: 4px; cursor: pointer;'>글목록</button>";
		echo "</div>";
	} else {
		echo "<div style='text-align: center; margin-top: 20px;'>";
		echo "<h3 style='color: red;'>글 수정 오류: " . $db->error() . "</h3>";
		echo "<button type='button' onclick=\"history.back()\" 
              style='padding: 8px 15px; background-color: #f44336; color: white; 
              border: none; border-radius: 4px; cursor: pointer;'>이전으로</button>";
		echo "<button type='button' onclick=\"location.href='board_list.php'\" 
              style='padding: 8px 15px; background-color: #2196F3; color: white; 
              border: none; border-radius: 4px; margin-left: 10px; cursor: pointer;'>글목록</button>";
		echo "</div>";
	}

	// 데이터베이스 연결 종료
	$db->close();
?>