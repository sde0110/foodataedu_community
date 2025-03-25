<?php
// DB 커넥션및 공통 환경변수등.

// register_globals=off 인경우 값 그냥 받기
@extract($_GET);
@extract($_POST);
@extract($_SERVER);

//홈 디랙토리
$HOME_DIR = $_SERVER["DOCUMENT_ROOT"]."/";

//홈주소
$HOME_URL = "";
$IMG_TEMP_URL = "";
// $ADM_DIR = "";

//데이타베이스 설정
/* // dataedu_cbt 데이타베이스 설정
$DB_SERVER = "p:dataedupt-database.ch9vbaymjdbm.ap-northeast-2.rds.amazonaws.com";         // DB 호스트
$DB_USER = "dataedu_cbt";          // DB에 접근 가능한 아이디
$DB_PASSWD = "epdlxjdpeb!1";    // 해당 아이디 비밀번호
$DB_NAME = "dataedu_cbt";    // DB 이름
*/

// mysql 데이타베이스 설정
/*
$DB_SERVER = "localhost";         // DB 호스트
$DB_USER = "root";          // DB에 접근 가능한 아이디
$DB_PASSWD = "epdlxjdpeb!1";    // 해당 아이디 비밀번호
$DB_NAME = "mysql"; 
*/

$DB_SERVER = "localhost:3307";    // localhost 사용
$DB_USER = "user";           // XAMPP 기본 사용자
$DB_PASSWD = "epdlxjdpeb!1";             // 비밀번호 없음(기본 설정)
$DB_NAME = "test";           // 사용할 데이터베이스

//COOKIE 생성 ID
$COOKIE_URL = "";
$ADMIN_DIR = "rajatec_a_page";

$HashAlgo = "sha256";


$Deny_ip_array = array ( // 관리자가 인위적으로 IP 지정
"0" => "87.56.104.169",
"1" => "211.34.178.158",
"2" => "70.169.135.83"
);

if(in_array($_SERVER['REMOTE_ADDR'],$Deny_ip_array)) {
echo "<div align='center' style='font-size:13pt;padding-top:200px;font-weight:bold;'>".$_SERVER['REMOTE_ADDR']." IP 에서 동시 트래픽이 많이 발생되고 있습니다. 접속 차단 되었습니다.<br><br>정상적인 사용자의 경우는 rajatech63848@gmail.com 로 사용하시는 IP를 보내시어 해지요청 부탁드립니다.<br><br>저희 사이트에서는 중복접근을 쿠키로 체크하고 있습니다.<br><br>사용하시는 브라우저에 쿠키가 차단되어있을경우 새로고침될때마다<br>접속껀수가 증가되어 접속이 차단될 수도 있으니,<br>쿠키사용을 활성화 하시기 바랍니다.<br>익스플로러의 경우  인터넷옵션 > 개인정보 > 고급 에서 수정가능.</div>";
exit;
}