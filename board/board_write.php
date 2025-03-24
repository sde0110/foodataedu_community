<?php require_once 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>글 작성</title>
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
		<h2>글쓰기</h2>
		<form name="board" method="post" action="board_sql_insert.php">
			<table>
				<tbody>
					<tr>
						<td width="100">이름</td>
						<td><input name="name" type="text" placeholder="이름을 입력하세요"></td>
					</tr>
					<tr>
						<td>비밀번호</td>
						<td><input name="pw" type="password" placeholder="비밀번호를 입력하세요"></td>
					</tr>
					<tr>
						<td>제목</td>
						<td><input name="title" type="text" placeholder="제목을 입력하세요"></td>
					</tr>
					<tr>
						<td>내용</td>
						<td><textarea name="content" placeholder="내용을 입력하세요"></textarea></td>
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