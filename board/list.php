<?php
// DB 연결 파일 포함
require_once 'db_connect.php';

// 현재 페이지 번호를 URL의 'page' 매개변수에서 가져오고, 기본값은 1로 설정
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// 한 페이지에 표시할 게시물 수 (10개)
$items_per_page = 10;

// DB 연결 객체 생성
$db = get_db_connection();

// 전체 게시물 수를 조회하여 총 레코드 수를 가져옴
$db->sql("SELECT COUNT(*) as total FROM board");
$db->fetch_row();
$total_records = $db->f('total');

// 총 페이지 수를 계산 (총 레코드 수를 페이지당 항목 수로 나누고 올림)
$total_pages = ceil($total_records / $items_per_page);

// 페이지 번호가 유효한 범위 내에 있도록 조정
$page = max(1, min($page, $total_pages));

// 현재 페이지에 표시할 게시물의 시작 위치를 계산
$offset = ($page - 1) * $items_per_page;

// 현재 페이지에 표시할 게시물 목록을 조회 (작성일 기준으로 정렬)
$db->sql("SELECT num, title, name, day FROM board ORDER BY day DESC LIMIT $offset, $items_per_page");

// 표시용 번호 초기화
$display_number = $total_records - $offset;
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <!-- 문서의 문자 인코딩을 UTF-8로 설정 -->
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시판</title>
    <!-- 공통 CSS 스타일을 포함 -->
    <?php echo get_common_css(); ?>
</head>
<body>
    <div class="container">
        <h1>게시판</h1>
        
        <table>
            <thead>
                <tr>
                    <th width="10%">번호</th>
                    <th width="55%">제목</th>
                    <th width="20%">작성자</th>
                    <th width="15%">작성일</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_records > 0): ?>
                    <!-- 게시물이 있을 경우 각 게시물을 테이블 행으로 출력 -->
                    <?php while ($db->fetch_row()): ?>
                        <tr>
                            <!-- 표시용 번호 사용 -->
                            <td><?php echo $display_number--; ?></td>
                            <!-- 제목을 클릭하면 해당 게시물의 상세 페이지로 이동 -->
                            <td><a href="view.php?num=<?php echo $db->f('num'); ?>"><?php echo htmlspecialchars($db->f('title')); ?></a></td>
                            <td><?php echo htmlspecialchars($db->f('name')); ?></td>
                            <td><?php echo $db->f('day'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>

                    <!-- 게시물이 없을 경우 메시지 출력 -->
                    <tr><td colspan="4" style="text-align:center">게시물이 없습니다.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- 페이지네이션 -->
        <div class="pagination">
            <?php if ($total_pages > 1): ?>
                <!-- 이전 페이지 링크 -->
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>">&laquo; 이전</a>
                <?php endif; ?>
                
                <?php
                // 페이지 번호 범위 설정 (현재 페이지에서 앞뒤로 3페이지씩 표시)
                $start_page = max(1, $page - 3);
                $end_page = min($total_pages, $start_page + 6);
                ?>
                
                <!-- 페이지 번호 링크 생성 -->
                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <?php if ($i == $page): ?>
                        <!-- 현재 페이지는 활성화된 상태로 표시 -->
                        <a class="active"><?php echo $i; ?></a>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <!-- 다음 페이지 링크 -->
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>">다음 &raquo;</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- 글쓰기 버튼 -->
        <div style="text-align: right;">
            <a href="write.php" class="btn">글쓰기</a>
        </div>
    </div>
    <?php $db->DB_close(); // DB 연결 종료 ?>
</body>
</html>