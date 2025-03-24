<!-- 글 상세 화면 구성 -->

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<?php
			$num = $_GET["num"];

			// 데이터베이스 클래스 포함
			require_once 'db_connect.php';

			// 데이터베이스 연결 객체 얻기
			$db = DBConnection::getInstance();
			$conn = $db->getConnection();

			$sql = "SELECT * FROM board WHERE num=$num";
			$result = $db->query($sql);

			$row = $db->fetch_assoc($result);

			echo "<h2> ".$row["title"]."</h2>";
			echo "<table border='1'>";
			echo "    <tbody>";
			echo "	      <tr>";
			echo "            <td width='600'>작성자 : ".$row["name"]."</td>";
			echo "        </tr>";
			echo "	      <tr>";
			echo "            <td>".$row["content"]."</td>";
			echo "        </tr>";
			echo "    </tbody>";
			echo "</table>";

			$db->close();
		?>
		<div>
			<button type="button" onclick="location.href='board_list.php'">글목록</button>
            <button type="button" onclick="location.href='board_pw.php?num='+'<?=$num?>'">글삭제</button>
			<button type="button" onclick="location.href='board_pw.php?mode=modify&num='+'<?=$num?>'">글 수정</button>
		</div>
	</body>
</html>