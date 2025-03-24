<!-- 데이터베이스 연결 파일 -->
 
<?php
// 데이터베이스 연결 정보
$server_name = "localhost:3307";
$user_id = "user";
$user_pw = "epdlxjdpeb!1";
$db_name = "test";

// 데이터베이스 연결 함수
function get_db_conn() {
    global $server_name, $user_id, $user_pw, $db_name;
    
    // 데이터베이스 연결
    $conn = mysqli_connect($server_name, $user_id, $user_pw, $db_name);
    
    // 연결 확인
    if (!$conn) {
        die("데이터베이스 연결 실패: " . mysqli_connect_error() . 
            "<br>서버: $server_name, 사용자: $user_id, DB: $db_name");
    }
    
    // 한글 인코딩 설정
    mysqli_set_charset($conn, "utf8");
    
    return $conn;
}

class DBConnection {
    private static $instance = null;
    private $conn;
    
    private $server_name = "localhost:3307";
    private $user_id = "user";
    private $user_pw = "epdlxjdpeb!1";
    private $db_name = "test";
    
    // 생성자는 private으로 설정하여 외부에서 new로 객체 생성 불가
    private function __construct() {
        $this->conn = mysqli_connect(
            $this->server_name, 
            $this->user_id, 
            $this->user_pw, 
            $this->db_name
        );
        
        if (!$this->conn) {
            die("데이터베이스 연결 실패: " . mysqli_connect_error());
        }
        
        mysqli_set_charset($this->conn, "utf8");
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
        return mysqli_query($this->conn, $sql);
    }
    
    // 결과 가져오기 (수정된 메서드)
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
        mysqli_close($this->conn);
    }
}
?>