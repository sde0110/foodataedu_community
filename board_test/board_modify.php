<!-- 글 수정 화면 구성 -->

<?php
	$num = $_GET["num"];

	// 데이터베이스 클래스 포함
	require_once 'db_connect.php';

	// 데이터베이스 연결 객체 얻기
	$db = DBConnection::getInstance();
	$conn = $db->getConnection();

	$sql = "SELECT * FROM board WHERE num=$num";
	$result = $db->query($sql);  // mysqli_query 대신 $db->query 사용

	$row = $db->fetch_assoc($result);  // mysqli_fetch_assoc 대신 $db->fetch_assoc 사용
	
	$name = $row["name"];
	$pw = $row["pass"];
	$title = $row["title"];
	$content = $row["content"];
	
	// 데이터베이스 연결 종료 (추가)
	$db->close();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<script>
			function fn_null_check() {
				if(!document.board.name.value) {
					alert("이름을 입력하세요");
					return;
				} else if(!document.board.pw.value) {
					alert("비밀번호를 입력하세요");
					return;
				} else if(!document.board.title.value) {
					alert("제목을 입력하세요");
					return;
				}

				document.board.submit()
			}
		</script>
	</head>
	<body>
		<h2>글 수정</h2>
		<form name="board" method="post" action="board_sql_update.php?num=<?=$num?>">
			<table border="1">
				<tbody>
					<tr>
						<td>이름</td>
						<td><input name="name" type="text" size="78" value="<?=$name?>"></td>
					</tr>
					<tr>
						<td>비밀번호</td>
						<td><input name="pw" type="password" size="78" value="<?=$pw?>"></td>
					</tr>
					<tr>
						<td>제목</td>
						<td><input name="title" type="text" size="78"  value="<?=$title?>"></td>
					</tr>
					<tr>
						<td>내용</td>
						<td><textarea name="content" cols="80" rows="6"><?=$content?></textarea></td>
					</tr>
				</tbody>
			</table>
		</form>
		<div>
			<button type="button" onclick="fn_null_check()">저장</button>
			<button type="button" onclick="location.href='board_list.php'">글 목록</button>
		</div>
	</body>
</html>