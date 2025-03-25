# 게시판CRUD

---

[board_신동은.zip](board_%EC%8B%A0%EB%8F%99%EC%9D%80.zip)

---

## ⚙️ 업데이트 로그

---

((03.25)) 업데이트 내용

- DB_Class_mysql.php 의 전역 변수 적용
- 파일 개수 간소화 (db_connect, delete, edit, list, view, write)
- 불필요한 반복 제거
- 페이지네이션 추가
- 주석 추가
- 작성일 기준 순서 정렬 추가

## ⚙️ 구조

---

```markdown
1. 모든 페이지 관리
   └── db_connect.php                    // 데이터베이스 연결 설정 및 공통 함수 포함
       └── /db_data/DB_Class_mysql.php   // 데이터베이스 클래스 포함

2. 게시글 목록
   list.php
   └── db_connect.php
       └── DB_Class_mysql.php

3. 글 작성
   write.php
   └── db_connect.php
       └── DB_Class_mysql.php

4. 상세 보기
   view.php
   └── db_connect.php
       └── DB_Class_mysql.php

5. 글 수정
   edit.php
   └── db_connect.php
       └── DB_Class_mysql.php

6. 글 삭제
   delete.php
   └── db_connect.php
       └── DB_Class_mysql.php
```

## ⚙️ Query 테이블 생성

---

```sql
// 한글 인코딩 부분 추가함

CREATE TABLE board (
num INT NOT NULL AUTO_INCREMENT,
title CHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
name CHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
pass CHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
content TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
day CHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
PRIMARY KEY(num)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## ⚙️ 로컬 서버 정보

---

```php
$server_name = "localhost:3307";
$user_id = "user";
$user_pw = "epdlxjdpeb!1";
$db_name = "test";
```
