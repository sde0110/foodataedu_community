<!-- 글 작성 화면 구성 -->

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
		<h2>글쓰기</h2>
		<form name="board" method="post" action="board_sql_insert.php">
			<table border="1">
				<tbody>
					<tr>
						<td>이름</td>
						<td><input name="name" type="text" size="78"></td>
					</tr>
					<tr>
						<td>비밀번호</td>
						<td><input name="pw" type="password" size="78"></td>
					</tr>
					<tr>
						<td>제목</td>
						<td><input name="title" type="text" size="78"></td>
					</tr>
					<tr>
						<td>내용</td>
						<td><textarea name="content" cols="80" rows="6"></textarea></td>
					</tr>
				</tbody>
			</table>
			<div>
				<button type="button" onclick="fn_null_check()">저장</button>
				<button type="button" onclick="location.href='board_list.php'">글목록</button>
			</div>
		</form>
	</body>
</html>