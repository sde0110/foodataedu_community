<!-- 글 수정 화면 구성 -->

<?php
	// 데이터베이스 클래스 포함
	require_once 'db_connect.php';

	// 데이터베이스 연결 객체 얻기
	$db = DBConnection::getInstance();
	$conn = $db->getConnection();

	$num = $_GET["num"];
	$sql = "SELECT * FROM board WHERE num=$num";
	$result = $db->query($sql);
	$row = $db->fetch_assoc($result);

	if (!$row) {
		echo "<script>
			alert('존재하지 않는 게시글입니다.');
			location.href='board_list.php';
		</script>";
		exit;
	}

	$name = $row["name"];
	$pw = $row["pass"];
	$title = $row["title"];
	$content = $row["content"];
	
	// 데이터베이스 연결 종료
	$db->close();
?>

<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>글 수정</title>
		<?php echo get_common_css(); ?>
		<script>
			function fn_null_check() {
				if(!document.board.name.value) {
					alert("이름을 입력하세요");
					document.board.name.focus();
					return;
				} else if(!document.board.pw.value) {
					alert("비밀번호를 입력하세요");
					document.board.pw.focus();
					return;
				} else if(!document.board.title.value) {
					alert("제목을 입력하세요");
					document.board.title.focus();
					return;
				}

				document.board.submit();
			}
		</script>
	</head>
	<body>
		<div class="container">
			<h2>글 수정</h2>
			<form name="board" method="post" action="board_sql_update.php?num=<?=$num?>">
				<table>
					<tbody>
						<tr>
							<td width="100">이름</td>
							<td><input name="name" type="text" value="<?=htmlspecialchars($name)?>"></td>
						</tr>
						<tr>
							<td>비밀번호</td>
							<td><input name="pw" type="password" value="<?=htmlspecialchars($pw)?>"></td>
						</tr>
						<tr>
							<td>제목</td>
							<td><input name="title" type="text" value="<?=htmlspecialchars($title)?>"></td>
						</tr>
						<tr>
							<td>내용</td>
							<td><textarea name="content"><?=htmlspecialchars($content)?></textarea></td>
						</tr>
					</tbody>
				</table>
				<div style="text-align: center; margin-top: 20px;">
					<button type="button" onclick="fn_null_check()" class="button">저장</button>
					<button type="button" onclick="location.href='board_list.php'" class="button delete">취소</button>
				</div>
			</form>
		</div>
	</body>
</html>