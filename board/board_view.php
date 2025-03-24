<!-- 글 상세 화면 구성 -->

<?php
// 데이터베이스 클래스 포함
require_once 'db_connect.php';

// 데이터베이스 연결 객체 얻기
$db = DBConnection::getInstance();
$conn = $db->getConnection();

$num = $_GET["num"];

// 조회수 증가
$updateSql = "UPDATE board SET count = IFNULL(count, 0) + 1 WHERE num = $num";
$db->query($updateSql);

// 게시글 조회
$sql = "SELECT * FROM board WHERE num = $num";
$result = $db->query($sql);
$row = $db->fetch_assoc($result);

if (!$row) {
    echo "<script>
        alert('존재하지 않는 게시글입니다.');
        location.href='board_list.php';
    </script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $row["title"]; ?></title>
	<?php echo get_common_css(); ?>
</head>
<body>
	<div class="container">
		<h2><?php echo $row["title"]; ?></h2>
		<div style="background-color: #f9f9f9; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
			<div style="display: flex; justify-content: space-between;">
				<span>작성자: <?php echo $row["name"]; ?></span>
				<span>작성일: <?php echo $row["day"]; ?> </span>
			</div>
		</div>
		<div style="min-height: 200px; line-height: 1.6; padding: 20px; background-color: #fff; border: 1px solid #eee; border-radius: 4px; margin-bottom: 20px;">
			<?php echo nl2br(htmlspecialchars($row["content"])); ?>
		</div>
		<div style="text-align: center;">
			<button type="button" onclick="location.href='board_list.php'" class="button">글목록</button>
			<button type="button" onclick="location.href='board_pw.php?mode=modify&num=<?php echo $num; ?>'" class="button edit">글 수정</button>
			<button type="button" onclick="location.href='board_pw.php?num=<?php echo $num; ?>'" class="button delete">글 삭제</button>
		</div>
	</div>
</body>
</html>
<?php $db->close(); ?>