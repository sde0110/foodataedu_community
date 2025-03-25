<?php
// DB 연결 파일 포함
require_once 'db_connect.php';

// 페이지 관련 변수
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;

// DB 연결
$db = get_db_connection();

// 전체 게시물 수 조회
$db->sql("SELECT COUNT(*) as total FROM board");
$db->fetch_row();
$total_records = $db->f('total');

// 페이지네이션 계산
$total_pages = ceil($total_records / $items_per_page);

// 페이지 확인 및 조정
if ($page < 1) $page = 1;
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

// 페이지에 표시할 게시물 조회
$offset = ($page - 1) * $items_per_page;
$db->sql("SELECT num, title, name, day FROM board ORDER BY num DESC LIMIT $offset, $items_per_page");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시판</title>
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
                <?php
                if($total_records > 0) {
                    while($db->fetch_row()) {
                        $num = $db->f('num');
                        $title = $db->f('title');
                        $name = $db->f('name');
                        $day = $db->f('day');
                        
                        echo "<tr>";
                        echo "<td>$num</td>";
                        echo "<td><a href='view.php?num=$num'>".htmlspecialchars($title)."</a></td>";
                        echo "<td>".htmlspecialchars($name)."</td>";
                        echo "<td>$day</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center'>게시물이 없습니다.</td></tr>";
                }
                
                // DB 연결 종료
                $db->DB_close();
                ?>
            </tbody>
        </table>
        
        <!-- 페이지네이션 -->
        <div class="pagination">
            <?php
            if($total_pages > 1) {
                // 이전 페이지 링크
                if($page > 1) {
                    echo "<a href='?page=".($page-1)."'>&laquo; 이전</a>";
                }
                
                // 페이지 번호 링크
                $start_page = max(1, $page - 3);
                $end_page = min($total_pages, $start_page + 6);
                
                for($i = $start_page; $i <= $end_page; $i++) {
                    if($i == $page) {
                        echo "<a class='active'>$i</a>";
                    } else {
                        echo "<a href='?page=$i'>$i</a>";
                    }
                }
                
                // 다음 페이지 링크
                if($page < $total_pages) {
                    echo "<a href='?page=".($page+1)."'>다음 &raquo;</a>";
                }
            }
            ?>
        </div>
        
        <div style="text-align: right;">
            <a href="write.php" class="btn">글쓰기</a>
        </div>
    </div>
</body>
</html>