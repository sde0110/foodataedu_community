<!-- 게시판 목록 화면 구성 -->

<?php
// 데이터베이스 클래스 포함
require_once 'db_connect.php';

// 데이터베이스 연결 객체 얻기
$db = DBConnection::getInstance();
$conn = $db->getConnection();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>게시판</title>
	<?php echo get_common_css(); ?>
</head>
<body>
	<div class="container">
		<h2>게시판</h2>
		<table>
			<thead>
				<tr>
					<th width="50">번호</th>
					<th width="400">제목</th>
					<th width="80">작성자</th>
					<th width="80">작성일</th>
				</tr>
			</thead>
			<tbody>
			<?php
			// 쿼리 실행
			$sql = "SELECT * FROM board ORDER BY num DESC;";
			$result = $db->query($sql);
			
			$hasRows = false;
			while ($row = $db->fetch_assoc($result)) {
				$hasRows = true;
				echo "<tr>";
				echo "	<td>".$row["num"]."</td>";
				echo "	<td><a href='board_view.php?num=".$row["num"]."'>".$row["title"]."</a></td>";
				echo "	<td>".$row["name"]."</td>";
				echo "	<td>".$row["day"]."</td>";
				echo "</tr>";
			}
			
			if (!$hasRows) {
				echo "<tr><td colspan='5' style='text-align:center'>등록된 게시글이 없습니다.</td></tr>";
			}
			
			// 연결 종료
			$db->close();
			?>
			</tbody>
		</table>
		<div style="text-align: right;">
			<button type="button" onclick="location.href='board_write.php'" class="button">글작성</button>
		</div>
	</div>
</body>	
</html>
