
<!-- 글 작성 DB 처리 페이지 -->
<?php
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

	// 현재 가장 큰 글 번호 확인하여 새 글 번호 결정
	$checkSql = "SELECT MAX(num) as max_num FROM board";
	$checkResult = $db->query($checkSql);
	$row = $db->fetch_assoc($checkResult);
	
	// 글이 없는 경우(NULL) 또는 결과가 없는 경우 1번부터 시작
	$newNum = 1;
	if ($row && $row['max_num']) {
		$newNum = $row['max_num'] + 1;
	}

	$sql = "INSERT INTO board(num, name, pass, title, content, day, count) VALUES($newNum, '$name', '$pw', '$title', '$content', '$today', 0);";
	
	$result = $db->query($sql);

	if($result) {
		echo "글이 성공적으로 업로드 되었습니다!</br>";
		echo "<button type='button' onclick=\"location.href='board_list.php'\">글목록</button>";
	} else {
		echo "글 업로드 중 실패 했습니다." . $db->error() . "</br>";
		echo "<button type='button' onclick=\"history.back()\">이전으로</button>";
	}

	// 데이터베이스 연결 종료
	$db->close();
?>