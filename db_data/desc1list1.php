<!DOCTYPE html>
<?php
/**
 * SubPage Template
 * 20240603 | @m | 최초작성. 요구반영. 결함개선. 고도화
 * 20250318 | @m |
 */

require_once $_SERVER['DOCUMENT_ROOT'] . "/include/SessionStart.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/include/connect_mysql.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/include/DB_Class_mysql_v2025.php";
 
$DBMY = new DB_mysql_class();
?>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<meta name="description" content="데이터에듀의 컴퓨터 기반 시험(CBT) 데이터에듀PT">
<title>서술형 문제 입력 | 데이터에듀PT 관리자</title>

<?php include $_SERVER['DOCUMENT_ROOT']."/admin/share/inc/html_head.php"; ?>

</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT']."/admin/share/inc/sub_header.php"; ?>
<!-- #body_content -->
<div id="body_content">
<!-- container -->
<div class="container clearfix">





<!-- cm1hg1 -->
<div class="cm1hg1">
	<div class="w1">
		<div class="tg1">
			<h2 class="h1">서술형 문제 입력</h2>
		</div>
	</div>
	<div class="w2">
		<!-- <a href="#layer1text1convert1" class="button toggle" data-send-focus="that" title="텍스트 변환 [레이어팝업]"><span class="t1">텍스트 변환</span></a>	 -->
	</div>
</div>
<!-- /cm1hg1 -->


<!-- 시험 입력 폼 -->
<div class="cm1write3">
    <form id="examForm">
        <div class="item">
            <span class="tt1">시험명</span>
            <input type="text" id="examTitle" name="title" placeholder="시험명 입력" class="w100" required>
        </div>
        <div id="criteriaContainer">
            <div class="criteria-item">
                <input type="text" name="criteria[]" class="w30" required placeholder="채점 기준">
                <textarea name="criteria2[]" cols="30" rows="3" required placeholder="채점 내용"></textarea>
                <button type="button" class="remove-criteria" style="display: none;">삭제</button>
            </div>
        </div>    
    </form>
    <button type="button" id="addCriteria" class="button wide">채점 기준 추가</button> 
    <button type="button" id="saveExamBtn" class="button wide"><span class="t1">저장</span></button>
    <button type="button" id="cancelExamBtn" class="button wide" style="display: none;"><span class="t1">취소</span></button>
</div>

<hr class="mgt0375em mgb1375em">

<!-- 시험 목록 -->
<div class="cm1bbs1list1">
    <div class="scroll-x-lt-large fscroll1-xy" tabindex="0">
        <div style="min-width:800px;" class="srclist"></div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 시험 목록 불러오기
    function loadExamList() {
        $.ajax({
            url: 'inc_desc1list1.php',
            type: 'GET',
            success: function(data) {
                $('.srclist').html(data);
            },
            error: function() {
                alert('시험 목록을 불러오는 중 오류가 발생했습니다.');
            }
        });
    }

    // 페이지 로드 시 시험 목록 로드
    loadExamList();

    // 체점 기준 추가
    $("#addCriteria").click(function() {
        let criteriaField = `<div class="criteria-item">
            <input type="text" name="criteria[]" class="w30" required placeholder="체점 기준">
            <textarea name="criteria2[]" cols="30" rows="3" required placeholder="체점 내용"></textarea>
            <button type="button" class="remove-criteria">삭제</button>
        </div>`;
        $("#criteriaContainer").append(criteriaField);
        $(".remove-criteria").show();
    });

    // 체점 기준 삭제
    $(document).on("click", ".remove-criteria", function() {
        $(this).parent().remove();
        if ($("#criteriaContainer .criteria-item").length === 1) {
            $(".remove-criteria").hide();
        }
    });

    // 저장 및 수정 버튼 중복 실행 방지
    $(document).off('click', '#saveExamBtn').on('click', '#saveExamBtn', function() {
        var examId = $(this).attr('data-id'); // 수정할 시험 ID
        var title = $('#examTitle').val().trim();

        if (title === '') {
            alert('시험명을 입력하세요.');
            return;
        }

        var criteria = [];
        var descriptions = [];
        var hasEmpty = false;

        $('input[name="criteria[]"]').each(function() {
            var value = $(this).val().trim();
            if (value === '') {
                hasEmpty = true;
            }
            criteria.push(value);
        });

        $('textarea[name="criteria2[]"]').each(function() {
            var value = $(this).val().trim();
            if (value === '') {
                hasEmpty = true;
            }
            descriptions.push(value);
        });

        if (hasEmpty || criteria.length === 0 || descriptions.length === 0) {
            alert('모든 체점 기준을 입력하세요.');
            return;
        }

        // AJAX 요청 (수정 또는 저장)
        $.ajax({
            url: 'frm.php',
            type: 'POST',
            data: {
                action: examId ? 'update_exam' : 'modins', // 시험 ID가 있으면 수정, 없으면 저장
                exam_id: examId,
                title: title,
                criteria: criteria,
                descriptions: descriptions
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(examId ? '시험이 수정되었습니다.' : '시험이 저장되었습니다.');

                    // 입력 폼 초기화
                    $('#examTitle').val('');
                    $('#criteriaContainer').html(`
                        <div class="criteria-item">
                            <input type="text" name="criteria[]" class="w30" required>
                            <textarea name="criteria2[]" cols="30" rows="3" required></textarea>
                            <button type="button" class="remove-criteria" style="display: none;">삭제</button>
                        </div>
                    `);

                    $('#saveExamBtn').text('저장').removeAttr('data-id'); // 다시 저장 버튼으로 변경
                    $('#cancelExamBtn').hide(); // 취소 버튼 숨김
                    loadExamList(); // 시험 목록 갱신
                } else {
                    alert('오류: ' + response.message);
                }
            },
            error: function() {
                alert('서버 오류가 발생했습니다.');
            }
        });
    });
    // "보기" 버튼 클릭 시 데이터 불러오기
    $(document).on('click', '.edit-exam', function() {
        var examId = $(this).data('id'); // 클릭한 시험의 ID 가져오기
        $('#saveExamBtn').text('수정').attr('data-id', examId); // 저장 버튼을 "수정"으로 변경
        $('#cancelExamBtn').show(); // 취소 버튼 활성화
        
        $.ajax({
            url: 'frm.php',
            type: 'POST',
            data: { action: 'get_exam', exam_id: examId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // 시험명 채우기
                    $('#examTitle').val(response.data.title);

                    // 기존 체점 기준 제거 후 새로 추가
                    $('#criteriaContainer').html('');
                    response.data.criteria.forEach(function(item) {
                        let criteriaField = `<div class="criteria-item">
                            <input type="text" name="criteria[]" class="w30" required value="${item.criterion}">
                            <textarea name="criteria2[]" cols="30" rows="3" required>${item.description}</textarea>
                            <button type="button" class="remove-criteria">삭제</button>
                        </div>`;
                        $("#criteriaContainer").append(criteriaField);
                    });

                    // 삭제 버튼 활성화
                    $(".remove-criteria").show();
                } else {
                    alert('오류: ' + response.message);
                }
            },
            error: function() {
                alert('서버 오류가 발생했습니다.');
            }
        });
    });   
    
    // 취소 버튼 클릭 시 입력 폼 초기화
    $("#cancelExamBtn").click(function() {
        $('#examTitle').val('');
        $('#criteriaContainer').html(`
            <div class="criteria-item">
                <input type="text" name="criteria[]" class="w30" required placeholder="체점 기준">
                <textarea name="criteria2[]" cols="30" rows="3" required placeholder="체점 내용"></textarea>
                <button type="button" class="remove-criteria" style="display: none;">삭제</button>
            </div>
        `);
        $('#saveExamBtn').text('저장').removeAttr('data-id'); // 다시 "저장"으로 변경
        $('#cancelExamBtn').hide(); // 취소 버튼 숨김
    });    
 

});
</script>


</div>
<!-- /container -->
</div>
<!-- /#body_content -->
<?php include $_SERVER['DOCUMENT_ROOT']."/admin/share/inc/sub_footer.php"; ?>
</body>
</html>