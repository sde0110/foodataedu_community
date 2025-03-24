<!-- 패스워드 확인 페이지 -->
<?php
	if(isset($_GET["num"]))
		$num = $_GET["num"];
	else 
		$num = "";

	if(isset($_GET["incorrect"]))
		$incorrect = $_GET["incorrect"];
	else
		$incorrect = "";

	if(isset($_GET["mode"]) && !empty($_GET["mode"]))
		$mode = $_GET["mode"];
	else 
		$mode = "delete";

	$url = "board_pw_check.php?mode=".$mode."&num=".$num;
?>

<!-- 화면 구성 -->
<!DOCTYPE html>
<html>
<head>
	<title>비밀번호 확인</title>
</head>

<body>
	<div class="container">
		<h2>비밀번호 확인</h2>
		<p><?php echo ($mode == "delete") ? "글을 삭제하려면" : "글을 수정하려면"; ?> 비밀번호를 입력하세요</p>
		<form method="post" action="<?=$url?>">
			<input name="pw" type="password" size="20"> 
			<button type="submit">확인</button>
			<button type="button" onclick="location.href='board_view.php?num=<?=$num?>'">취소</button>
		</form>
		<?php
			if($incorrect == "true") {
				echo "<div id='notification'>";
				echo "    비밀번호가 다릅니다. 다시 입력해 주세요";
				echo "</div>";
			}
		?>
	</div>
</body>	
</html>
