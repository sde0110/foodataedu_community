<!-- 게시판 목록 화면 구성 -->

<!DOCTYPE html>
<html>
<head>
	<title>게시판</title>
</head>
<body>
	<h2>게시판</h2>
	<table border="1">
		<thead>
			<tr>
				<th width="50">번호</th>
				<th width="400">제목</th>
				<th width="80">작성자</th>
				<th width="80">작성일</th>
			</tr>
		</thead>
		<?php
		// 데이터베이스 클래스 포함
		require_once 'db_connect.php';

		// 데이터베이스 연결 객체 얻기
		$db = DBConnection::getInstance();
		$conn = $db->getConnection();

		// 쿼리 실행
		$sql = "SELECT * FROM board ORDER BY num DESC;";
		$result = $db->query($sql);

		while ($row = $db->fetch_assoc($result)) {
			echo "<tr>";
			echo "	<td>".$row["num"]."</td>";
			echo "  <td><a href='board_view.php?num=".$row["num"]."'>".$row["title"]."</a></td>";
			echo "	<td>".$row["name"]."</td>";
			echo "	<td>".$row["day"]."</td>";
			echo "</tr>";
		}

		// 연결 종료
		$db->close();
	?>
		</tbody>
	</table>
	<div>
		<button type="button" onclick="location.href='board_write.php'">글작성</button>
	</div>
</body>	
</html>
