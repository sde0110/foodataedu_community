<!--DB Create query
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `test`;

CREATE TABLE IF NOT EXISTS `board` (
    `num` int(11) NOT NULL AUTO_INCREMENT,
-->

<?php
####### 한글 인코딩 설정 #######
################################
header('Content-Type: text/html; charset=UTF-8');
ini_set('default_charset', 'UTF-8');
################################

##### 데이터베이스 연결 정보 #####
################################
$DB_SERVER = "localhost:3307";
$DB_USER = "user";
$DB_PASSWD = "epdlxjdpeb!1";
$DB_NAME = "test";
################################

// DB 클래스 파일 포함
require_once $_SERVER["DOCUMENT_ROOT"]."/db_data/DB_Class_mysql.php";

// DB 연결 함수
function get_db_connection() {
    // DB_Class_mysql.php 파일에 선언된 전역 변수 사용
    global $DB_SERVER, $DB_USER, $DB_PASSWD, $DB_NAME;
    
    // DB 클래스 인스턴스 생성
    $db = new DB_mysql_class();
    
    // 추가 인코딩 설정
    $db->sql("SET NAMES utf8mb4");
    $db->sql("SET CHARACTER SET utf8mb4");
    $db->sql("SET COLLATION_CONNECTION='utf8mb4_unicode_ci'");
    
    return $db;
}

// DB Tool 함수 (CRUD 작업용)
function get_db_tool() {
    global $DB_SERVER, $DB_USER, $DB_PASSWD, $DB_NAME;
    
    // DB Tool 인스턴스 생성
    $db = new DB_mysql_tool();
    
    // 추가 인코딩 설정
    $db->sql("SET NAMES utf8mb4");
    $db->sql("SET CHARACTER SET utf8mb4");
    $db->sql("SET COLLATION_CONNECTION='utf8mb4_unicode_ci'");
    
    return $db;
}

// 한글 문자열 처리 함수
function utf8_encode_deep(&$input) {
    if (is_string($input)) {
        $input = utf8_encode($input);
    } else if (is_array($input)) {
        foreach ($input as &$value) {
            utf8_encode_deep($value);
        }
        unset($value);
    } else if (is_object($input)) {
        $vars = array_keys(get_object_vars($input));
        foreach ($vars as $var) {
            utf8_encode_deep($input->$var);
        }
    }
}

// 전역 스타일시트 정의
function get_common_css() {
    return <<<CSS
    <style>
        body { font-family: 'Malgun Gothic', 'Apple SD Gothic Neo', sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .btn, .button { display: inline-block; padding: 8px 16px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; margin: 0 5px; }
        .btn:hover, .button:hover { background-color: #45a049; }
        .btn-cancel, .button.delete { background-color: #f44336; }
        .btn-cancel:hover, .button.delete:hover { background-color: #d32f2f; }
        .btn-edit, .button.edit { background-color: #2196F3; }
        .btn-edit:hover, .button.edit:hover { background-color: #0b7dda; }
        .pagination { text-align: center; margin: 20px 0; }
        .pagination a { display: inline-block; padding: 8px 16px; text-decoration: none; color: #000; }
        .pagination a.active { background-color: #4CAF50; color: white; }
        .pagination a:hover:not(.active) { background-color: #ddd; }
        .error { color: red; margin-bottom: 10px; }
        form { background: #f9f9f9; padding: 20px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"], textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea { height: 200px; resize: vertical; }
        .post-info { background-color: #f2f2f2; padding: 10px; border-radius: 4px; display: flex; justify-content: space-between; margin-bottom: 20px; }
        .post-content { min-height: 200px; padding: 20px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px; }
    </style>
    CSS;
}
?>