<!-- 패스워드 확인 페이지 -->
<?php
require_once 'db_connect.php';
// 데이터베이스 연결 객체 얻기
if(isset($_GET["num"]))
	$num = $_GET["num"];
else 
	$num = "";

// 비밀번호 확인 여부 확인
if(isset($_GET["incorrect"]))
	$incorrect = $_GET["incorrect"];
else
	$incorrect = "";

// 모드 확인
if(isset($_GET["mode"]) && !empty($_GET["mode"]))
	$mode = $_GET["mode"];
else 
	$mode = "delete";

// 확인 페이지 이동
$url = "board_pw_check.php?mode=".$mode."&num=".$num;
?>

<!-- 화면 구성 -->
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>비밀번호 확인</title>
	<?php echo get_common_css(); ?>
	<style>
		body {
			display: flex;
			justify-content: center;
			align-items: center;
			min-height: 100vh;
		}
		.pw-container {
			max-width: 400px;
			width: 100%;
			padding: 30px;
		}
		.pw-form {
			margin-bottom: 20px;
		}
		.pw-input {
			margin-bottom: 20px;
		}
	</style>
</head>

<!-- 화면 구성 -->
<body>
	<div class="container pw-container">
		<h2>비밀번호 확인</h2>
		<p><?php echo ($mode == "delete") ? "글을 삭제하려면" : "글을 수정하려면"; ?> 비밀번호를 입력하세요</p>
		<form method="post" action="<?=$url?>" class="pw-form">
			<input name="pw" type="password" placeholder="비밀번호 입력" class="pw-input"> 
			<div style="text-align: center;">
				<button type="submit" class="button">확인</button>
				<button type="button" onclick="location.href='board_view.php?num=<?=$num?>'" class="button delete">취소</button>
			</div>
		</form>
		<?php
			if($incorrect == "true") {
				echo "<div class='notification'>";
				echo "    비밀번호가 일치하지 않습니다. 다시 입력해 주세요.";
				echo "</div>";
			}
		?>
	</div>
</body>	
</html>
