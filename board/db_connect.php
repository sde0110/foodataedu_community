<!-- 데이터베이스 연결 파일 -->
 
<?php

// DB 커넥션 및 공통 환경변수 설정
##########################################################################
// 경로 설정
$_SERVER["DOCUMENT_ROOT"] = $_SERVER["DOCUMENT_ROOT"] ?: "C:/xampp/htdocs";
$HOME_DIR = $_SERVER["DOCUMENT_ROOT"]."/";

// 데이터베이스 설정
$DB_SERVER = "localhost"; 
$DB_USER = "root";
$DB_PASSWD = "epdlxjdpeb!1";
$DB_NAME = "test";

##########################################################################

// DBConnection 클래스 정의
##########################################################################
class DBConnection {
    private static $instance = null;
    private $conn;
    
    private $server_name = "localhost:3307"; // 포트 제거
    private $user_id = "user";
    private $user_pw = "epdlxjdpeb!1";
    private $db_name = "test";
    
    // 생성자는 private으로 설정하여 외부에서 new로 객체 생성 불가
    private function __construct() {
        try {
            // mysqli 연결 시도
            $this->conn = mysqli_connect(
                $this->server_name, 
                $this->user_id, 
                $this->user_pw, 
                $this->db_name
            );
            
            if (!$this->conn) {
                throw new Exception("데이터베이스 연결 실패: " . mysqli_connect_error());
            }
            
            mysqli_set_charset($this->conn, "utf8");
            
        } catch (Exception $e) {
            // 연결 실패 시 오류 메시지 표시
            die("<div style='color: red; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px; border-radius: 5px;'>
                <h3>데이터베이스 연결 오류</h3>
                <p>" . $e->getMessage() . "</p>
                <p>서버: {$this->server_name}, 사용자: {$this->user_id}, DB: {$this->db_name}</p>
                <p>환경 설정을 확인해 주세요:</p>
                <ol>
                    <li>MySQL 서버가 실행 중인지 확인</li>
                    <li>사용자 이름과 비밀번호가 올바른지 확인</li>
                    <li>포트 번호가 올바른지 확인 (현재 설정: 기본 포트)</li>
                    <li>데이터베이스 이름이 존재하는지 확인</li>
                </ol>
                </div>");
        }
    }
    
    // 싱글톤 패턴으로 인스턴스 얻기
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new DBConnection();
        }
        return self::$instance;
    }
    
    // 연결 객체 얻기
    public function getConnection() {
        return $this->conn;
    }
    
    // 쿼리 실행
    public function query($sql) {
        $result = mysqli_query($this->conn, $sql);
        if (!$result) {
           
            error_log("SQL 오류: " . mysqli_error($this->conn) . " - 쿼리: " . $sql);
        }
        return $result;
    }
    
    // 결과 가져오기
    public function fetch_assoc($result) {
        // 쿼리 결과가 유효한지 확인
        if ($result === false) {
            // 쿼리 실패 시 오류 메시지 출력 후 null 반환
            echo "<div style='color: red; font-weight: bold;'>쿼리 실행 오류: " . $this->error() . "</div>";
            return null;
        }
        
        // 정상적인 경우 결과 반환
        return mysqli_fetch_assoc($result);
    }
    
    // 영향받은 행 수
    public function affected_rows() {
        return mysqli_affected_rows($this->conn);
    }
    
    // 마지막 삽입 ID
    public function insert_id() {
        return mysqli_insert_id($this->conn);
    }
    
    // 오류 메시지
    public function error() {
        return mysqli_error($this->conn);
    }
    
    // 연결 닫기
    public function close() {
        if ($this->conn) {
            mysqli_close($this->conn);
        }
    }
}

// 전역 스타일시트 정의
function get_common_css() {
    return <<<CSS
    <style>
        body {
            font-family: 'Malgun Gothic', 'Apple SD Gothic Neo', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        h2 {
            color: #333;
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f1f1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f8f8f8;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
        }
        .button:hover {
            background-color: #45a049;
        }
        .button.delete {
            background-color: #f44336;
        }
        .button.delete:hover {
            background-color: #d32f2f;
        }
        .button.edit {
            background-color: #2196F3;
        }
        .button.edit:hover {
            background-color: #0b7dda;
        }
        a {
            text-decoration: none;
            color: #333;
        }
        a:hover {
            text-decoration: underline;
        }
        input[type="text"], input[type="password"], textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 150px;
        }
        .notification {
            color: #f44336;
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
    CSS;
}
?>