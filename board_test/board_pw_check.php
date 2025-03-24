<!-- 패스워드 확인 DB 처리 페이지 -->
<?php
	if(isset($_GET["mode"]) && !empty($_GET["mode"])) {
		$mode = $_GET["mode"];
	} else {
		$mode = "delete"; // 기본값 설정
	}
	
	if(isset($_GET["num"]) && is_numeric($_GET["num"])) {
		$num = $_GET["num"];
	} else {
		echo "<script>
			alert('잘못된 접근입니다.');
			location.href='board_list.php';
		</script>";
		exit;
	}
	
	// POST로 전달 받은값 변수에 저장
    $pass = htmlspecialchars($_POST["pw"], ENT_QUOTES);

	// 데이터베이스 클래스 포함
	require_once 'db_connect.php';

	// 데이터베이스 연결 객체 얻기
	$db = DBConnection::getInstance();
	$conn = $db->getConnection();

	$sql = "SELECT pass FROM board WHERE num=$num";
	$result = $db->query($sql);

	// 쿼리 실행 오류 처리
	if(!$result) {
		echo "<script>
			alert('데이터베이스 오류: " . $db->error() . "');
			location.href='board_list.php';
		</script>";
		$db->close();
		exit;
	}

	// 결과 가져오기
	$row = $db->fetch_assoc($result);
	
	// 결과가 없을 경우 처리
	if(!$row) {
		echo "<script>
			alert('게시글이 존재하지 않습니다.');
			location.href='board_list.php';
		</script>";
		$db->close();
		exit;
	}

	$db_pass = $row["pass"];

	// 데이터베이스 연결 종료
	$db->close();

	if($pass == $db_pass) {
		// 비밀번호 일치할 때 처리
		if($mode == 'delete') {
			echo "<script>
				location.href='board_sql_delete.php?num=$num';
			</script>";
		} else if($mode == 'modify') {
			echo "<script>
				location.href='board_modify.php?num=$num';
			</script>";
		} else {
			echo "<script>
				alert('알 수 없는 모드입니다.');
				location.href='board_list.php';
			</script>";
		}
	} else {
		// 비밀번호 다를 때 처리
		echo "<script>
			location.href='board_pw.php?mode=$mode&num=$num&incorrect=true';
		</script>";
	}
?>