<!-- 글 작성 DB 처리 페이지 -->
<?php
// 데이터베이스 클래스 포함
require_once 'db_connect.php';

// 데이터베이스 연결 객체 얻기
$db = DBConnection::getInstance();
$conn = $db->getConnection();

// POST로 전달 받은값 변수에 저장
$name = htmlspecialchars($_POST["name"], ENT_QUOTES);
$pw = htmlspecialchars($_POST["pw"], ENT_QUOTES);
$title = htmlspecialchars($_POST["title"], ENT_QUOTES);
$content = htmlspecialchars($_POST["content"], ENT_QUOTES);

// 오늘 날짜 확인
$today = date("Y.m.d");

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

// 데이터베이스 연결 종료
$db->close();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>글 작성 결과</title>
	<?php echo get_common_css(); ?>
	<style>
		body {
			display: flex;
			justify-content: center;
			align-items: center;
			min-height: 100vh;
		}
		.result-container {
			max-width: 500px;
			width: 100%;
			padding: 30px;
			text-align: center;
		}
		.success-message {
			color: #4CAF50;
			font-size: 24px;
			margin-bottom: 20px;
		}
		.error-message {
			color: #f44336;
			font-size: 24px;
			margin-bottom: 20px;
		}
	</style>
</head>
<body>
	<div class="container result-container">
		<?php if($result): ?>
			<div class="success-message">글이 성공적으로 등록되었습니다!</div>
			<p>작성하신 게시글이 데이터베이스에 저장되었습니다.</p>
		<?php else: ?>
			<div class="error-message">글 등록에 실패했습니다.</div>
			<p>오류 메시지: <?php echo $db->error(); ?></p>
		<?php endif; ?>
		<div style="margin-top: 20px;">
			<button type="button" onclick="location.href='board_list.php'" class="button">글 목록 보기</button>
			<?php if(!$result): ?>
			<button type="button" onclick="history.back()" class="button delete">이전으로 돌아가기</button>
			<?php endif; ?>
		</div>
	</div>
</body>
</html>