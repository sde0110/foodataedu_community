<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/include/SessionStart.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/include/connect_mysql.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

require_once $_SERVER['DOCUMENT_ROOT'] . "/include/DB_Class_mysql_v2025.php";    
    
$db = new DB_mysql_class();
    
    if ($_POST['action'] === 'modins') {
        try {
            $title = trim($_POST['title']);
            $criteria = $_POST['criteria'] ?? [];
            $descriptions = $_POST['descriptions'] ?? [];

            if (empty($title)) {
                throw new Exception('시험명을 입력하세요.');
            }

            if (empty($criteria) || empty($descriptions)) {
                throw new Exception('체점 기준을 입력하세요.');
            }

            // 시험명 저장
            $exam_id = $db->create('_cbt_subjective_exams', [
                'title' => $title,
                'status' => 'ready'
            ]);

            if (!$exam_id) {
                throw new Exception('시험 저장 중 오류 발생');
            }

            // 여러 개의 체점 기준을 반복하여 삽입
            foreach ($criteria as $key => $criterion) {
                $description = $descriptions[$key] ?? '';
                if (empty($criterion) || empty($description)) continue;

                $db->create('_cbt_exam_criteria', [
                    'exam_id' => $exam_id,
                    'criterion' => $criterion,
                    'description' => $description
                ]);
            }

            echo json_encode(['status' => 'success']);
        } catch (mysqli_sql_exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'DB 오류: ' . $e->getMessage()]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => '오류 발생: ' . $e->getMessage()]);
        }
    }
    
    // 시험 정보 불러오기 (보기 버튼 클릭 시)
    if ($_POST['action'] === 'get_exam') {
        $exam_id = $_POST['exam_id'];

        // 시험 정보 가져오기
        $exam = $db->read('_cbt_subjective_exams', "idx = ?", [$exam_id]);
        
        if (!$exam) {
            echo json_encode(['status' => 'error', 'message' => '시험 정보를 찾을 수 없습니다.']);
            exit;
        }

        // 체점 기준 정보 가져오기
        $criteria = $db->read('_cbt_exam_criteria', "exam_id = ?", [$exam_id]);

        echo json_encode([
            'status' => 'success',
            'data' => [
                'title' => $exam[0]['title'],
                'criteria' => $criteria
            ]
        ]);
        exit;
    }
      
    // 시험 삭제 기능
    if ($_POST['action'] === 'delete_exam') {
        $examId = $_POST['exam_id'] ?? 0;
        
        if (!$examId) {
            echo json_encode(['status' => 'error', 'message' => '시험 ID가 없습니다.']);
            exit;
        }

        // 시험 삭제 (논리 삭제를 원하면 UPDATE 문으로 변경 가능)
        $deleted = $db->delete('_cbt_subjective_exams', "idx = ?", [$examId]);

        //if ($deleted) {
            echo json_encode(['status' => 'success', 'message' => '시험이 삭제되었습니다.']);
        //} else {
        //    echo json_encode(['status' => 'error', 'message' => '시험 삭제에 실패했습니다('.$deleted.').']);
        //}
        exit;
    }

    // 시험 및 체점 기준 수정 기능
    if ($_POST['action'] === 'update_exam') {
        $exam_id = $_POST['exam_id'];
        $title = trim($_POST['title']);
        $criteria = $_POST['criteria'] ?? [];
        $descriptions = $_POST['descriptions'] ?? [];

        if ($title === '' || empty($criteria) || empty($descriptions)) {
            echo json_encode(['status' => 'error', 'message' => '시험명과 체점 기준을 입력하세요.']);
            exit;
        }

        // 1. 시험명 업데이트
        $updateExam = $db->update('_cbt_subjective_exams', ['title' => $title], 'idx = ?', [$exam_id]);

        // 2. 기존 체점 기준 삭제 후 새로 추가
        $db->delete('_cbt_exam_criteria', 'exam_id = ?', [$exam_id]);

        foreach ($criteria as $index => $criterion) {
            if (!empty($criterion) && !empty($descriptions[$index])) {
                $db->create('_cbt_exam_criteria', [
                    'exam_id' => $exam_id,
                    'criterion' => trim($criterion),
                    'description' => trim($descriptions[$index])
                ]);
            }
        }

        echo json_encode(['status' => 'success', 'message' => '시험 및 체점 기준이 수정되었습니다.']);
        exit;
    }      

        
$db->close();  
}

?>