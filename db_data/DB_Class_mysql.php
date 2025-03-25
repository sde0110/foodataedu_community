<?php

// DB 관련 CLASS

class DB_mysql_class {
    var $conn;    // connection identifier
    var $res;    // result identifier
    var $arr;         // fetch 결과를 임시로 저장하는 Array
    var $autoFreeResult;
    var $Arr;         // 모든 결과를 fetch하여 저장 (sql_exec method에서 사용)
    var $dbHost;
    var $dbUser;
    var $dbPass;
    var $dbName;
    var $transactionOn;                //트랜젝션 스타트upload_mysql_class
    var $enCommit;

    /* DB_class 객체 생성자 */
    function  __construct($road=0) { // 0 : 120 / 1 : 121 or 122
        global $DB_SERVER;
        global $DB_USER;
        global $DB_PASSWD;
        global $DB_NAME;
        
        $autoFreeResult=1;
        $transactionOn=0;
        
    // DB connect ip selected
    /*
    if ( $road == 0 ) $DB_RESULT_SERVER = $DB_SERVER[0];
    else {
        $temp = mt_rand(1,2);
        $DB_RESULT_SERVER = $DB_SERVER[$temp];
      }
    */
    
        $this->dbHost = $DB_SERVER;        // 호스트
        $this->dbUser = $DB_USER;        // DB 계정
        $this->dbPass = $DB_PASSWD;        // DB 패스워드
        $this->dbName = $DB_NAME;        // DB 네임
        

        $this->conn = new mysqli("localhost:3307", "user", "epdlxjdpeb!1", "test");
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        $this->conn->query("set names utf8mb4");
                
        $this->autoFreeResult = $autoFreeResult;

        //echo $road." ".$this->dbHost;
    }

    function sql($sql) {
        $this->query = $sql;
        $this->res = $this->conn->query($this->query);        // 마지막 쿼리 저장
        $rs = $this->res;

        if (!$rs) {
            if ($this->transactionOn) $this->enCommit = false;
            $this->sql_error();
        }
        return $rs;                
    }

    /* 결과를 fetch */
    function fetch_row() {
        $this->arr = @mysqli_fetch_array($this->res);

        if (!is_array($this->arr)) {
            if ($this->autoFreeResult != 0)
            $this->free();

            return false;
        }

        else 
            return true;
    }

    /* SQL을 실행하고 모든 결과를 fetch하여 자동으로 $Arr에 저장 */
    function sql_exec($sql) {
        $Arr = array();

        $this->res = $this->sql($sql);

        $fieldCount = mysqli_num_fields($this->res);

        $i = 0;
        while ($this->fetch_row()) {
            for ($k = 0; $k < $fieldCount; $k++) {
                $fieldName = mysqli_fetch_field_direct($this->res, $k);
                $fieldName = $fieldName->name;
                $Arr[$i][$fieldName] = stripslashes($this->arr[$k]);
            }
            $i++;
        }

        return $Arr;
    }

//==================== 별도 하나씩 실행하는 스크립터용 시작

    function sql_etc($sql) {
            $this->query_etc = $sql;
            $this->res_etc = $this->conn->query($this->query_etc);        // 마지막 쿼리 저장
            $rs = $this->res_etc;
            if(!$rs) {  
                    if($this->transactionOn) $this->enCommit = false;
                    $this->sql_error();
            }
            return $rs;                
    }

    /* 결과를 fetch */
    function fetch_row_etc() {
        $this->arr_etc = @mysqli_fetch_array($this->res_etc);
        if (!is_array($this->arr_etc)) {
            if ($this->autoFreeResult != 0)
            $this->free_etc();

            return false;
        }

        else 
            return true;
    }

    /* SQL을 실행하고 모든 결과를 fetch하여 자동으로 $Arr에 저장 */
    function sql_exec_etc($sql) {
        $Arr = array();

        $this->res_etc = $this->sql_etc($sql);

        $fieldCount = mysqli_num_fields($this->res_etc);

        $i = 0;
        while ($this->fetch_row_etc()) {
            for ($k = 0; $k < $fieldCount; $k++) {
                $fieldName = $this->mysqli_field_name($this->res_etc, $k);
                $Arr[$i][$fieldName] = stripslashes($this->arr_etc[$k]);
            }
            $i++;
        }

        return $Arr;
    }

    /* 자원을 반환 */
    function free_etc() {
        @mysqli_free_result($this->res_etc);
    }
    
    function mysqli_field_name($result, $field_offset)
    {
        $properties = mysqli_fetch_field_direct($result, $field_offset);
        return is_object($properties) ? $properties->name : null;
    }    

//======================== 별도 하나씩 실행하는 스크립터용 끝

    function fetchArray($sql)
    {
        $arr = $this->sql_exec($sql);
        return $arr[0];
    }

    /* 자원을 반환 */
    function free() {
        @mysqli_free_result($this->res);
    }

    /* DB 닫기 */
    function DB_close() {
        @mysqli_close($this->conn);
    }

    /* 에러 발생시 메세지 출력 */
    function sql_error($err_msg='') {
        
       
        $err_no = mysqli_errno($this->conn);
        $err_msg = mysqli_error($this->conn); 
        $error_msg = "ERROR CODE " . $err_no . " : ".$err_msg; 
        
    //if ( $_SERVER["REMOTE_ADDR"] == "118.39.59.248" ) {

        echo "
        <p align=center>
        <font color=red>
        <b>$err_msg</b>
        </font>
        </p>
        ";
        
        $this->sql('rollback');

    //}

        exit;
    }

    /* arr에서 해당 field의 값을 가져옴 */
    function f($field_name) {
        return stripslashes($this->arr[$field_name]);
    }
    function f_etc($field_name) {
        return stripslashes($this->arr_etc[$field_name]);
    }
    
    /* 실행 결과 총 row의 값을 반환 */
    function numRows() {
        return @mysqli_num_rows($this->res);
    }

    /* delete및 select, insert, update등을 수행했을때 영향을 받는 row들의 갯수를 반환, 단 delete로 모두 지웠을 경우에는 0을 반환 */
    function affectedRows(){
        return @mysqli_affected_rows($this->conn);
    }

    function DB_insert_id()
    {
        return $this->conn->insert_id;
    }

    /********************************************
     GETTER METHOD
     *******************************************/
    //GET MESSAGE
    function getMessage()
    {
        return $this->message;
    }

    //롤백관련
    function rollback($val=0)
    {
        if($val==0) {
            $qry='set autocommit=0';
        }
        else if($val==1) {
            $qry='commit';
           // $this->rollback(3);
        }
        else if($val==2) {
            $qry='rollback';
        }
        else if($val==3) $qry='set autocommit=1';
        return $this->sql($qry);
    }    
    
    #############################################
    //DO METHOD
    //비지니스 계층 메소드
    #############################################

    /********************************************
     DO LIST:리스트 출력
     *******************************************/
    function doList($recordPerPage,$currentPage)
    {
        $this->i = isset($this->i) ? $this->i : 0;  // $this->i null issue로 인한 코드
        if($this->i==0)
        {
            $firstRecord=$recordPerPage*($currentPage-1);
            $this->query=$this->query.' LIMIT '.$firstRecord.','.$recordPerPage;
            $this->result=$this->sql($this->query);
        }
        if(!$row=mysqli_fetch_array($this->result) )
        {
            $this->query=explode('LIMIT',$this->query);
            $this->query=$this->query[0];
            $this->result=$this->sql($this->query);
            $this->i=0;
            return false;
        }

        //RETURN ROWS
        $this->i++;
        return $row;
    }

    function doList2()
    {
        if($this->i==0)
        {
            $this->query=$this->query;
            $this->result=$this->sql($this->query);
        }
        if(!$row=mysqli_fetch_array($this->result) )
        {
            $this->query=explode('LIMIT',$this->query);
            $this->query=$this->query[0];
            $this->result=$this->sql($this->query);
            $this->i=0;
            return false;
        }

        //RETURN ROWS
        $this->i++;
        return $row;
    }

    /********************************************
     GET PAGE INDEX:페이지 인텍스 HTML코드 반환
     *******************************************/
    function getPageIndex($recordPerPage,$totalRecord,$currentPage,$imgprev,$imgnext)
    {

        //SET ARRAY REFER TO PARAMETERS:페이지 인덱스 생성을 위한 기본값 정렬
        $pageIndex['totalRecord'] = $totalRecord;
        $pageIndex['recordPerPage'] = $recordPerPage;
        $pageIndex['currentPage'] = $currentPage;

        //CALCULATE PAGE IDNEX:
        $pageIndex['totalPage']=ceil($pageIndex['totalRecord']/$pageIndex['recordPerPage']);

        /****************************************
        //DEFINE NEW QUERY_STRING
        페이지 링크 정의를 위한 URL파싱
         ***************************************/
        parse_str($_SERVER['QUERY_STRING'],$QUERY_STRING);
        unset($QUERY_STRING['page']);
        $temp = '';
        foreach($QUERY_STRING as $key=>$value)
        {
            if(empty($temp)){
                $temp="$key=".urlencode($value);
            }else{
                $temp.="&$key=".urlencode($value);
            }
        }
        $QUERY_STRING=$temp;
        

        /****************************************
        CREATE PAGE INDEX HTML CODE
        HTML코드 생성
         ***************************************/
        ##PREVIOUS BLOCK

        $pageIndex['htmlCode'] = '<span class="control">
            <span class="m first"><a href="'.$_SERVER['PHP_SELF'].'?'.$QUERY_STRING.'&page=1" title="처음 페이지"><i class="ic">처음</i></a></span>';
            if($pageIndex['currentPage'] > 1){
                $pageIndex['htmlCode'].= '<span class="m prev"><a href="'.$_SERVER['PHP_SELF'].'?'.$QUERY_STRING.'&page='.($pageIndex['currentPage']-1).'" title="이전 페이지"><i class="ic">이전</i></a></span></span>';
            }else{
                $pageIndex['htmlCode'].= '<span class="m prev"><a title="이전 페이지 없음"><i class="ic">이전</i></a></span></span>';
            }
        
        ##PAGE INDEX
        $pageIndex['htmlCode'] .= '<span class="pages"><span class="tt1">';
       
        $pageIndex['htmlCode'].= $pageIndex['currentPage'].'/'.$pageIndex['totalPage'];
        $pageIndex['htmlCode'] .= '</span></span>';

        ##NEXT BLOCK
        $pageIndex['htmlCode'].='<span class="control">';

        if($pageIndex['totalPage'] != $pageIndex['currentPage']){
        $pageIndex['htmlCode'] .= '<span class="m next"><a href="'.$_SERVER['PHP_SELF'].'?'.$QUERY_STRING.'&page='.($pageIndex['currentPage']+1).'" title="다음 페이지"><i class="ic">다음</i></a></span>';
        }else{
        $pageIndex['htmlCode'].='<span class="m next"><a title="다음 페이지 없음"><i class="ic">다음</i></a></span>';
        }

        $pageIndex['htmlCode'] .= '<span class="m last"><a href="'.$_SERVER['PHP_SELF'].'?'.$QUERY_STRING.'&page='.$pageIndex['totalPage'].'" title="마지막 페이지"><i class="ic">마지막</i></a></span></span>';

        ##RETURN PAGE INDEX ARRAY
        return $pageIndex;

    }//END METHOD

    function getPageIndexAjax($recordPerPage,$pagePerBlock,$currentPage,$imgprev,$imgnext)
    {

        //SET ARRAY REFER TO PARAMETERS:페이지 인덱스 생성을 위한 기본값 정렬
        $pageIndex[totalRecord] = $this->numRows();
        $pageIndex[recordPerPage] = $recordPerPage;
        $pageIndex[pagePerBlock] = $pagePerBlock;
        $pageIndex[currentPage] = $currentPage;

        //CALCULATE PAGE IDNEX:
        $pageIndex[totalPage]=ceil($pageIndex[totalRecord]/$pageIndex[recordPerPage]);
        $pageIndex[currentBlock]=ceil($pageIndex[currentPage]/$pageIndex[pagePerBlock]);
        $pageIndex[totalBlock]=ceil($pageIndex[totalPage]/$pageIndex[pagePerBlock]);

        //FIRST PAGE/LAST PAGE
        $pageIndex[firstPage]=($pageIndex[currentBlock]*$pageIndex[pagePerBlock]) - ($pageIndex[pagePerBlock]-1);
        $pageIndex[lastPage]=($pageIndex[currentBlock]*$pageIndex[pagePerBlock]);

        /****************************************
        //DEFINE NEW QUERY_STRING
         페이지 링크 정의를 위한 URL파싱
         ***************************************/
        parse_str($_SERVER[QUERY_STRING],$QUERY_STRING);
        unset($QUERY_STRING[page]);
        foreach($QUERY_STRING as $key=>$value)
        {
            if(!$temp){
                $temp="$key=".urlencode($value);
            }else{
                $temp.="&$key=".urlencode($value);
            }
        }
        $QUERY_STRING=$temp;

        /****************************************
         CREATE PAGE INDEX HTML CODE
         HTML코드 생성
         ***************************************/
        ##PREVIOUS BLOCK
      
            $pageIndex[htmlCode] = '<div class="pagenav" title="페이지 내비게이션"> <span class="pfirst" title="맨앞 페이지"><a href="javascript:;" onclick="get_page(1);return false;"><span class="ic">맨앞</span></a></span> ';
        
        if($pageIndex[currentPage] > $pageIndex[pagePerBlock])
        {
            $pageIndex[htmlCode].= '<span class="pprev" title="이전 페이지"><a href="javascript:;" onclick="get_page('.(($pageIndex[currentBlock]-2)*$pageIndex[pagePerBlock]+1).');return false;"><span class="ic">이전</span></a></span> ';
        }else{
            $pageIndex[htmlCode].= '<span class="pprev" title="이전 페이지 없음"><a><span class="ic">이전</span></a></span>  ';
        }
        
        
        ##PAGE INDEX
        for($i=$pageIndex[firstPage]; $i<=$pageIndex[lastPage]; $i++)
        {

          if ( $i == $pageIndex[firstPage] ) $class_temp = ' class="first"';
          else if ( $i == $pageIndex[lastPage] ) $class_temp = ' class="last"';
          else $class_temp = '';

            if($i<=$pageIndex[totalPage])
            {
                if($i==$pageIndex[currentPage])
                {
                    $pageIndex[htmlCode].='<span class="on"><a title="현재 '.$i.' 페이지"><strong>'.$i.'</strong></a></span> ';
                }else{
                    $pageIndex[htmlCode].='<span'.$class_temp.'><a href="javascript:;" onclick="get_page('.$i.');return false;" title="'.$i.' 페이지">'.$i.'</a></span> ';
                }
            }
        }
        
        
        ##NEXT BLOCK
        if($pageIndex[currentBlock] <= ($pageIndex[totalBlock]-1) )
        {
            $pageIndex[htmlCode].='<span class="pnext" title="다음 페이지"><a href="javascript:;" onclick="get_page('.($pageIndex[currentBlock]*$pageIndex[pagePerBlock]+1).');return false;"><span class="ic">다음</span></a></span> ';
        }else{
            $pageIndex[htmlCode].='<span class="pnext" title="다음 페이지 없음"><a><span class="ic">다음</span></a></span> ';
        }
        
        $pageIndex[htmlCode] .= '<span class="plast" title="맨뒤 페이지"><a href="javascript:;" onclick="get_page('.$pageIndex[totalPage].');return false;"><span class="ic">맨뒤</span></a></span></div> ';

        ##RETURN PAGE INDEX ARRAY
        return $pageIndex;

    }//END METHOD
        
    /********************************************
     GET PAGE INDEX:페이지 인텍스 HTML코드 반환
     *******************************************/
    function getPageIndex_table($recordPerPage,$pagePerBlock,$currentPage,$imgprev,$imgnext)
    {
        //SET ARRAY REFER TO PARAMETERS:페이지 인덱스 생성을 위한 기본값 정렬
        $pageIndex[totalRecord] = $this->numRows();
        $pageIndex[recordPerPage] = $recordPerPage;
        $pageIndex[pagePerBlock] = $pagePerBlock;
        $pageIndex[currentPage] = $currentPage;

        //CALCULATE PAGE IDNEX:
        $pageIndex[totalPage]=ceil($pageIndex[totalRecord]/$pageIndex[recordPerPage]);
        $pageIndex[currentBlock]=ceil($pageIndex[currentPage]/$pageIndex[pagePerBlock]);
        $pageIndex[totalBlock]=ceil($pageIndex[totalPage]/$pageIndex[pagePerBlock]);

        //FIRST PAGE/LAST PAGE
        $pageIndex[firstPage]=($pageIndex[currentBlock]*$pageIndex[pagePerBlock]) - ($pageIndex[pagePerBlock]-1);
        $pageIndex[lastPage]=($pageIndex[currentBlock]*$pageIndex[pagePerBlock]);

        /****************************************
        //DEFINE NEW QUERY_STRING
         페이지 링크 정의를 위한 URL파싱
         ***************************************/
        parse_str($_SERVER[QUERY_STRING],$QUERY_STRING);
        unset($QUERY_STRING[page]);
        foreach($QUERY_STRING as $key=>$value)
        {
            if(!$temp){
                $temp="$key=".urlencode($value);
            }else{
                $temp.="&$key=".urlencode($value);
            }
        }
        $QUERY_STRING=$temp;

        /****************************************
         CREATE PAGE INDEX HTML CODE
         HTML코드 생성
         ***************************************/
        ##PREVIOUS BLOCK
      
            $pageIndex[htmlCode] = '<table class="page_navi" summary="Page Navigation"><tr class="img"><td class="pfirst"><a href="'.$_SERVER[PHP_SELF].'?'.$QUERY_STRING.'&page=1"><img src="/img/board/page_first.gif" width="16" height="14" alt="First Page" /></a></td><td class="first">';
        
        if($pageIndex[currentPage] > $pageIndex[pagePerBlock])
        {
            $pageIndex[htmlCode].='<a tabindex="0" href="'.$_SERVER[PHP_SELF].'?'.$QUERY_STRING.'&page='.( ($pageIndex[currentBlock]-2)*$pageIndex[pagePerBlock]+1).'" title="Prev Page" target="_self" ><img src="/img/board/page_prev.gif" width="15" height="11" alt="이전 페이지" /></a>';
        }
        
        $pageIndex[htmlCode] .= '</td>';
        
        ##PAGE INDEX
        for($i=$pageIndex[firstPage]; $i<=$pageIndex[lastPage]; $i++)
        {

          if ( $i == $pageIndex[firstPage] ) $class_temp = '';
          else if ( $i == $pageIndex[totalPage] ) $class_temp = '';
          else $class_temp = '';

            if($i<=$pageIndex[totalPage])
            {
                if($i==$pageIndex[currentPage])
                {
                    $pageIndex[htmlCode].='<td class="active"><strong><a href="#none" title="Now Page">'.$i.'</a></strong></td>';
                }else{
                    $pageIndex[htmlCode].='<td'.$class_temp.'><a tabindex="0" href="'.$_SERVER[PHP_SELF].'?'.$QUERY_STRING.'&page='.$i.'"  title=\''.$i.' page\' target=\'_self\'> '.$i.' </a></td>';
                }
            }
        }
        
        $pageIndex[htmlCode] .= '<td class="last">';
        
        ##NEXT BLOCK
        if($pageIndex[currentBlock] <= ($pageIndex[totalBlock]-1) )
        {
            $pageIndex[htmlCode].='<a href="'.$_SERVER[PHP_SELF].'?'.$QUERY_STRING.'&page='.($pageIndex[currentBlock]*$pageIndex[pagePerBlock]+1).'" title="Next Page"  target="_self" tabindex="0" ><img src="/img/board/page_next.gif" width="15" height="11" alt="다음 페이지" /></a>';
        }
        
        $pageIndex[htmlCode] .= '</td><td class="plast"><a href="'.$_SERVER[PHP_SELF].'?'.$QUERY_STRING.'&page='.$pageIndex[totalPage].'"><img src="/img/board/page_last.gif" width="16" height="14" alt="Last Page" /></a></td></tr></table>';

        ##RETURN PAGE INDEX ARRAY
        return $pageIndex;

    }//END METHOD

    /********************************************
     GET PAGE INDEX:페이지 인텍스 HTML코드 반환2
     *******************************************/
    function getPageIndex2($recordPerPage,$pagePerBlock,$currentPage,$imgprev,$imgnext)
    {
        //SET ARRAY REFER TO PARAMETERS:페이지 인덱스 생성을 위한 기본값 정렬
        $pageIndex[totalRecord] = $this->numRows();
        $pageIndex[recordPerPage] = $recordPerPage;
        $pageIndex[pagePerBlock] = $pagePerBlock;
        $pageIndex[currentPage] = $currentPage;

        //CALCULATE PAGE IDNEX:
        $pageIndex[totalPage]=ceil($pageIndex[totalRecord]/$pageIndex[recordPerPage]);
        $pageIndex[currentBlock]=ceil($pageIndex[currentPage]/$pageIndex[pagePerBlock]);
        $pageIndex[totalBlock]=ceil($pageIndex[totalPage]/$pageIndex[pagePerBlock]);

        //FIRST PAGE/LAST PAGE
        $pageIndex[firstPage]=($pageIndex[currentBlock]*$pageIndex[pagePerBlock]) - ($pageIndex[pagePerBlock]-1);
        $pageIndex[lastPage]=($pageIndex[currentBlock]*$pageIndex[pagePerBlock]);

        /****************************************
        //DEFINE NEW QUERY_STRING
         페이지 링크 정의를 위한 URL파싱
         ***************************************/
        parse_str($_SERVER[QUERY_STRING],$QUERY_STRING);
        unset($QUERY_STRING[page2]);
        foreach($QUERY_STRING as $key=>$value)
        {
            if(!$temp){
                $temp="$key=".urlencode($value);
            }else{
                $temp.="&$key=".urlencode($value);
            }
        }
        $QUERY_STRING=$temp;


        
        /****************************************
         CREATE PAGE INDEX HTML CODE
         HTML코드 생성
         ***************************************/
        ##PREVIOUS BLOCK
      
            $pageIndex[htmlCode] = '<div class="pagenav" title="페이지 내비게이션"><span class="pfirst" title="맨앞 페이지"><a href="'.$_SERVER[PHP_SELF].'?'.$QUERY_STRING.'&page2=1"><span class="ic">맨앞</span></a></span> ';
        
        if($pageIndex[currentPage] > $pageIndex[pagePerBlock])
        {
            $pageIndex[htmlCode].='<span class="pprev" title="이전 페이지"><a href="'.$_SERVER[PHP_SELF].'?'.$QUERY_STRING.'&page2='.( ($pageIndex[currentBlock]-2)*$pageIndex[pagePerBlock]+1).'"><span class="ic">'.$imgprev.'</span></a></span> ';
        }
        
        
        ##PAGE INDEX
        for($i=$pageIndex[firstPage]; $i<=$pageIndex[lastPage]; $i++)
        {

          if ( $i == $pageIndex[firstPage] ) $class_temp = ' class="first"';
          else if ( $i == $pageIndex[lastPage] ) $class_temp = ' class="last"';
          else $class_temp = '';

            if($i<=$pageIndex[totalPage])
            {
                if($i==$pageIndex[currentPage])
                {
                    $pageIndex[htmlCode].='<span class="on"><a title="현재 '.$i.' 페이지"><strong>'.$i.'</strong></a></span> ';
                }else{
                    $pageIndex[htmlCode].='<span'.$class_temp.'><a href="'.$_SERVER[PHP_SELF].'?'.$QUERY_STRING.'&page2='.$i.'" title="'.$i.' 페이지">'.$i.'</a></span> ';
                }
            }
        }
        
        $pageIndex[htmlCode] .= '<td class="last">';
        
        ##NEXT BLOCK
        if($pageIndex[currentBlock] <= ($pageIndex[totalBlock]-1) )
        {
            $pageIndex[htmlCode].= '<span class="pnext" title="다음 페이지"><a href="'.$_SERVER[PHP_SELF].'?'.$QUERY_STRING.'&page2='.($pageIndex[currentBlock]*$pageIndex[pagePerBlock]+1).'"><span class="ic">'.$imgnext.'</span></a></span> ';
        }
        
        $pageIndex[htmlCode] .= '<span class="plast" title="맨뒤 페이지"><a href="'.$_SERVER[PHP_SELF].'?'.$QUERY_STRING.'&page2='.$pageIndex[totalPage].'"><span class="ic">맨뒤</span></a></span> </div>';

        ##RETURN PAGE INDEX ARRAY
        return $pageIndex;
    }//END METHOD    
    
//////////////////////////////////////////////
//  사용자 정의 함수 

  
// 포인트 관련 함수

    // 회원 포인트 지급
    function cPoint($userid='',$field='',$point='')
    {
        if(empty($userid) || empty($field) || empty($point)) return 0;
        $tempSql = "update _member_pay set ".$field." = ".$field." + (".$point.") where member_id = '".$userid."' ";
        if ($this->sql_etc($tempSql)) return 0; else return 1;
        $this->free_etc();
    }

    #포인트로그 
    function logPoint($userid='',$code='',$point='',$info='',$object='')
    {
        // 이전 포인트 가져오기
        //$temp_array = array('301','201','202','801','806');
        //$temp_array2 = array('213','214','215','216','711','712','713','714','111','511','808'); // 보너스 관련        
        //if ( in_array($code,$temp_array) ) $field = 'cash';
        //else if ( in_array($code,$temp_array2) ) $field = 'point';        
        //else $field = 'game_point';
        
        $strSQL = "select mileage from _member where member_id='".$userid."' ";
        $this->sql_etc($strSQL);
        $this->fetch_row_etc();
        $prev_point = $this->f_etc(0);
        $this->free_etc();
        
        if ( $prev_point == '') $prev_point = 0;
        
        if(empty($userid) || empty($code) || empty($point)) return 0;
        $tempSql = "insert into _point_log set code='".$code."', userid='".$userid."', point=".$point.", point_prev=".$prev_point.", info='".$info."', objectid='".$object."', regdate=unix_timestamp()";
        if ($this->sql_etc($tempSql)) return 0; else return 1;
        $this->free_etc();
     }
    
        # 기간 게임포인트 로그
    function inPointLog($userid, $code, $cash, $info, $expiredate, $method) { // 보너스 차감을 위한 로그 
        
        	if ( $method == 'BP' ) {
        		$oTableName = "_point_bonus_log";
        		$oTableName_proc = "_point_bonus_proc_log";
        	} else {
        		$oTableName = "_gamepoint_bonus_log";
        		$oTableName_proc = "_gamepoint_bonus_proc_log";        		
        	}
           
           $expiredate = strtotime("+".$expiredate." day",time());
           $expiredate = date('Y-m-d', $expiredate);
           $expiredate = strtotime($expiredate.' 23:59:59');
           
           $strSQL = "insert into ".$oTableName."(userid, code, point, info, regdate, expiredate) values ('".$userid."','".$code."','".$cash."','".$info."',unix_timestamp(),'".$expiredate."')";
           $temp = $this->sql_etc($strSQL);
           $this->free_etc();
           
           // 로그 저장 
           $strSQL = "insert into ".$oTableName_proc."(userid, point, info, regdate) values ('".$userid."','".$cash."','".$info."',unix_timestamp())";
           $this->sql_etc($strSQL);
           $this->free_etc();
           
           return $temp;

    }
    
    #  기간 게임 포인트  차감 로그
    function delPointLog($userid, $cash, $bbs_idx=0, $method='') {
        
        if ( $cash > 0 ) {

        	if ( $method == 'BP' ) {
        		$oTableName = "_point_bonus_log";
        		$oTableName_proc = "_point_bonus_proc_log";
        	} else {
        		$oTableName = "_gamepoint_bonus_log";
        		$oTableName_proc = "_gamepoint_bonus_proc_log";        		
        	}
        	            
            $strSQL = "select * From ".$oTableName." where userid = '".$userid."' And expiredate > unix_timestamp() and point > 0 order by expiredate asc";
            $strQue = $this->sql_exec_etc($strSQL);
            $oTotal = count($strQue);
            $this->free_etc();
            
            for ( $i = 0; $i < $oTotal; $i++ ) {
                if ( $strQue[$i]['expiredate'] > 0 ) {
                    $plus[$strQue[$i]['idx']] = $strQue[$i]['point'];
                }
            }

            //$strSQL->free();
            $temp_bn_key = '';
            if(count($plus) > 0)
            {
                foreach($plus As $bn_key => $value)
                {
                    if(!$remain)
                        $remain = $value - $cash;
                    else
                        $remain += $value;

                   $temp_bn_key .= '|'.$bn_key;
                        
                    if($remain < 0)
                    {
                        $strSQL = "update ".$oTableName." set point = 0 where idx = ".$bn_key;
                        $result = $this->sql_etc($strSQL);
                        $this->free_etc();
                        //$result -> free();
                    }
                    else
                    {
                        $strSQL = "update ".$oTableName." set point = ".$remain." where idx = ".$bn_key;
                        $result = $this->sql_etc($strSQL);
                        $this->free_etc();
                      //  $result -> free();

                        break;
                    }
                    
                }
                
               // 로그 저장 
               $strSQL = "insert into ".$oTableName_proc."(userid, point, info, regdate) values ('".$userid."','".$cash."','".$temp_bn_key." 게임배팅 차감[".$bbs_idx."]',unix_timestamp())";
               $this->sql_etc($strSQL);
               $this->free_etc();
                           
            }    
        } // if 
    }  
    
    function nickTOid($str)
    {
        if ( isNull($str) ) return false;
        $strSQL = "select member_id from _member where nick_name='".$str."' ";
        $this->sql_etc($strSQL);
        $this->fetch_row_etc();
        $member_id = $this->f_etc(0);
        $this->free_etc();
        
        return $member_id;
    }

    function idTOnick($str)
    {
        if ( isNull($str) ) return false;
        $strSQL = "select nick_name from _member where member_id='".$str."' ";
        $this->sql_etc($strSQL);
        $this->fetch_row_etc();
        $nick_name = $this->f_etc(0);
        $this->free_etc();
        
        return $nick_name;
    }    
    
    function reallog($str1, $str2) {
        
        if ( isNull($str1) || isNull($str2) ) return false;
        $strSQL = "insert into _realtime_log set title='".$str1."', content='".$str2."', register_date=unix_timestamp()";
        $this->sql_etc($strSQL);
        $this->free_etc();
        
    }
// 사용자 정의 함수 끝
/////////////////////////////////////////////////
}


class DB_mysql_tool extends DB_mysql_class {        
        // insert query 생성
        function inQueryString($tableName, $input) {      
                $fStr = $this->filedStr($input, 1);
                $vStr = $this->valueStr($tableName, $input);

                $str = "insert into ".$tableName." (".$fStr.") values (".$vStr.")";
                //echo $str;
                return $str;
        }
        
        // Insert 쿼리의 values 부분
        /*
        function valueStr($tableName, $input) {
                $keys = array_keys($input);
                $rs = $this->sql("desc ".$tableName);
                for($i=0; $i<count($keys); $i++) {        
                        for($j=0; $j<mysql_num_rows($rs); $j++) {
                                if($keys[$i] == mysql_result($rs,$j,'Field')) {
                                        if(eregi("int", mysql_result($rs,$j,'Type'))) {
                                                $str .= ($input[$keys[$i]] == '' && $input[$keys[$i]] != 0) ? "NULL" : $input[$keys[$i]];
                                        } else {
                                                if($input[$keys[$i]] == '') $str .= "NULL";
                                                else $str .= "'".$input[$keys[$i]]."'";
                                        }
                                        $str = $str.",";
                                        break;
                                } 
                        }
                }
                if(substr($str,-1) == ',') $str = substr($str,0,strlen($str)-1);
                return $str;
        }*/
        function valueStr($tableName, $input) {
                $keys = array_keys($input);
                $rs = $this->sql("desc ".$tableName);
                for($i=0; $i<count($keys); $i++) {        
                        for($j=0; $j<mysqli_num_rows($rs); $j++) {
                                if($keys[$i] == $this->mysqli_result($rs,$j,'Field')) {
                                        if(preg_match("/int/i", $this->mysqli_result($rs,$j,'Type'))) {
                                                $str .= ( isNull($input[$keys[$i]]) ) ? "NULL" : $input[$keys[$i]]; 
                                                // == "" && $input[$keys[$i]] != 0
                                        } else {
                                                if($input[$keys[$i]] == '') { 
                                                    $str .= "NULL";
                                                } else {
                                                    if ( $input[$keys[$i]] == "now()" ) $str .= $input[$keys[$i]];
                                                    else if ( $input[$keys[$i]] == "sysdate()" ) $str .= $input[$keys[$i]];
                                                    else if ( $input[$keys[$i]] == "unix_timestamp()" ) $str .= $input[$keys[$i]];
                                                    else if ( stristr($input[$keys[$i]], 'SHA2(' ) != false ) $str .= $input[$keys[$i]];
                                                    else $str .= "'".$input[$keys[$i]]."'";
                                                }
                                        }
                                        $str = $str.",";
                                       // echo $this->mysqli_result($rs,$j,'Field')." ".$this->mysqli_result($rs,$j,'Type')." ".$str."<br />";
                                        break;
                                } 
                        }
                }
                if(substr($str,-1) == ',') $str = substr($str,0,strlen($str)-1);
                return $str;
        }

        // mysqli_result 함수가 없어서 mysql_result 대용
        function mysqli_result($res, $row, $field=0) { 
            $res->data_seek($row); 
            $datarow = $res->fetch_array(); 
            return $datarow[$field]; 
        }        
        
        // SQL 쿼리의 필드 정의 부분
        function filedStr($input, $mode) {
                $keys = ($mode == 1) ? array_keys($input) : $input;
                for($i=0; $i<count($keys); $i++) {
                        $str .= $keys[$i]; 
                        $str = $str.",";
                }
                if(substr($str,-1) == ',') $str = substr($str,0,strlen($str)-1);
                return $str;
        }
        
        // Insert 쿼리를 실행한다.
        function insert($tableName, $dArray) {
                $this->sql("LOCK TABLES ".$tableName." WRITE");
                $rs = $this->sql($this->inQueryString($tableName, $dArray));
                $max_rs = $this->DB_insert_id();
        $this->sql("UNLOCK TABLES");
                return $max_rs;                // 에러 번호
        }
        
        function updateStr($dArray, $tableName) {
                $rs = $this->sql("desc ".$tableName);
                while(list($field, $value) = each($dArray)) {
                        //for($i=0; $i<count($keys); $i++) {        
                        for($j=0; $j<mysqli_num_rows($rs); $j++) {
                                if($field == $this->mysqli_result($rs,$j,'Field')) {
                                        if(preg_match("/int/i", $this->mysqli_result($rs,$j,'Type'))) {
                                            if ( $value == '' ) $str .= $field."=NULL";
                                            else $str .= $field.'='.$value;
                                        } else {
                                            if ( $value == "now()" ) $str .= $field."=now()";
                                            else if($value == '') $str .= $field."=NULL";
                                          else $str .= $field."='".$value."'";
                                        }
                                        $str = $str.", \n";
                                        break;
                                } 
                        }
                }
                $str = trim($str);
                if(substr($str,-1) == ',') $str = substr($str,0,strlen($str)-1);
                return $str;
        }
        
        // Update
        function update($tableName, $dArray, $field, $value) {
                $query = "update ".$tableName." set ".$this->updateStr($dArray, $tableName)." where ".$field."='".$value."'"; 
                $this->sql($query);
        }

        function update_where($tableName, $dArray, $where) {
        $query = "update ".$tableName." set ".$this->updateStr($dArray, $tableName)." where ".$where;
        $this->sql($query);
        }

        // Delete
        function delete($tableName, $field, $value) {
                $query = "delete from ".$tableName." where ".$field."=".$value; 
                //echo $query;
                $this->sql($query);
        }

        // Delete_where
        function delete_where($tableName, $where) {
                $query = "delete from ".$tableName." where ".$where; 
                //echo $query;
                $this->sql($query);
        }

}

// FILE UPLOAD 관련 CLASS

class upload_mysql_class
{
    var $upload_tmpFileName = null;
    var $upload_fileName = null;
    var $upload_fileSize = null;
    var $upload_fileType = null;
    var $upload_fileWidth = null;
    var $upload_fileHeight = null;
    var $upload_fileExt = null;
    var $upload_fileImageType = null;
    var $upload_count = 0;
    var $upload_directory = null;
    var $upload_subdirectory = null;
    var $denied_ext = array
        (
            "php"    => ".phps",
            "phtml"    => ".phps",
            "html"    => ".txt",
            "htm"    => ".txt",
            "inc"    => ".txt",
            "sql"    => ".txt",
            "cgi"    => ".txt",
            "pl"    => ".txt",
            "jsp"    => ".txt",
            "asp"    => ".txt",
            "phtm"    => ".txt",
            "exe"    => ".txt",
            "conf"    => ".txt",
            "inc"    => ".txt"
        );

    function upload_mysql_class($upload_dir)
    {
        $this->upload_directory = $upload_dir;
        //$this->upload_subdirectory = time();
    }

    function define($files)
    {
        if(is_array($files['tmp_name']))
        {
            for($i = 0; $i < sizeof($files['tmp_name']); $i++)
            {
                if ( !is_null($files['name'][$i]) ) {

                    $this->_uploadErrorChecks($files['error'][$i], $files['name'][$i]);

                    if(is_uploaded_file($files['tmp_name'][$i]))
                    {
                        $this->upload_tmpFileName[$i] = $files['tmp_name'][$i];
//                        $this->upload_fileName[$i] = $this->_isFilsName($this->_isHangulName($this->_emptyToUnderline($this->_checkFileName($files['name'][$i]))));
                        $this->upload_fileName[$i] = $this->_isFilsName($this->_emptyToUnderline($this->_checkFileName($files['name'][$i]))); // 한글변환 제외
                        $this->upload_fileSize[$i] = (int) $files['size'][$i] ? $files['size'][$i] : 0;
                        $this->upload_fileType[$i] = $files['type'][$i];
                        $this->upload_fileExt[$i] = $this->_getExtension($files['name'][$i]);

                        // 이미지 파일
                        if($this->_isThisImageFile($files['type'][$i]) == true)
                        {
                            $img = $this->_getImageSize($files['tmp_name'][$i]);
                            $this->upload_fileWidth[$i] = (int) $img[0];
                            $this->upload_fileHeight[$i] = (int) $img[1];
                            $this->upload_fileImageType[$i] = $img[2];
                        }
                    }
                }
            }
        }
        else
        {
            $this->_uploadErrorChecks($files['error'], $files['name']);

            if(is_uploaded_file($files['tmp_name']))
            {
                $this->upload_tmpFileName = $files['tmp_name'];
//                $this->upload_fileName = $this->_isFilsName($this->_isHangulName($this->_emptyToUnderline($this->_checkFileName($files['name']))));
                $this->upload_fileName = $this->_isFilsName($this->_emptyToUnderline($this->_checkFileName($files['name']))); // 한글 변환 제외
                $this->upload_fileSize = (int) ($files['size']) ? $files['size'] : 0;
                $this->upload_fileType = $files['type'];
                $this->upload_fileExt = $this->_getExtension($files['name']);

                // 이미지 파일
                if($this->_isThisImageFile($files['type']) == true)
                {
                    $img = $this->_getImageSize($files['tmp_name']);
                    $this->upload_fileWidth = $img[0];
                    $this->upload_fileHeight = $img[1];
                    $this->upload_fileImageType = $img[2];
                }
            }
        }
    }

    function _isThisImageFile($type)
    {
        if(preg_match("/image/i", $type) == false && preg_match("/flash/", $type) == false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function _uploadErrorChecks($errorCode, $fileName)
    {
        if($errorCode == UPLOAD_ERR_INI_SIZE)
        {
            errormsg($fileName . " : 업로드 제한용량(".ini_get('upload_max_filesize').")을 초과한 파일입니다.");
        }
        else if($errorCode == UPLOAD_ERR_FORM_SIZE)
        {
            errormsg($fileName . " : 업로드한 파일이 HTML 에서 정의되어진 파일 업로드 제한용량을 초과하였습니다.");
        }
        else if($errorCode == UPLOAD_ERR_PARTIAL)
        {
            errormsg("파일이 일부분만 전송되었습니다. ");
        }
    }

    function _mkUploadDir()
    {
        if(is_dir($this->upload_directory) == false)
        {
            if(@mkdir($this->upload_directory, 0755) == false)
            {
                errormsg($this->upload_directory . " 디렉토리를 생성하지 못하였습니다. 퍼미션을 확인하시기 바랍니다.");
            }
        }
    }

    function _mkUploadSubDir()
    {
        if(is_writable($this->upload_directory) == false)
        {
            errormsg($this->upload_directory . " 에 쓰기 권한이 없습니다. " . $this->upload_subdirectory . "디렉토리를 생성하지 못하였습니다.");
        }
        else
        {
            $uploaded_path = $this->upload_directory . "/" . $this->upload_subdirectory;

            if(is_dir($uploaded_path) == false)
            {
                if(@mkdir($uploaded_path, 0755) == false)
                {
                    errormsg($uploaded_path . " 디렉토리를 생성하지 못하였습니다. 퍼미션을 확인하시기 바랍니다.");
                }
            }
            else
            {
                if(is_writable($uploaded_path) == false)
                {
                    errormsg($uploaded_path . " 디렉토리에 쓰기 권한이 없습니다. 파일을 업로드 할 수 없습니다.");
                }
            }
        }
    }

    function uploadedFiles($address=null)
    {
        if(is_array($this->upload_tmpFileName))
        {
            $this->_mkUploadDir();
            $this->_mkUploadSubDir();

            for($i = 0; $i < sizeof($this->upload_tmpFileName); $i++)
            {

                if ( !is_null($this->upload_fileName[$i]) ) {

                    $uploaded_filename = $this->upload_directory . "/" . $this->upload_subdirectory . "/" . $this->upload_fileName[$i];

                    if(@move_uploaded_file($this->upload_tmpFileName[$i], $uploaded_filename) == false)
                    {
                        errormsg($uploaded_filename . " 을 저장하지 못하였습니다.");
                    }
                    else
                    {
                        $this->upload_count += 1;
                    }
                   // if ( $address != null ) $this->_different_upload($this->upload_fileName[$i], $uploaded_filename, $address); // 다른 서버로 파일 업로드 
                    
                    @unlink($this->upload_tmpFileName[$i]);
                }
            }
        }
        else
        {
            if(is_uploaded_file($this->upload_tmpFileName))
            {
                $this->_mkUploadDir();
                $this->_mkUploadSubDir();

                $uploaded_filename = $this->upload_directory . "/" . $this->upload_subdirectory . "/" . $this->upload_fileName;

                if(@move_uploaded_file($this->upload_tmpFileName, $uploaded_filename) == false)
                {
                    errormsg($uploaded_filename . " 을 저장하지 못하였습니다.");
                }
                else
                {
                    $this->upload_count += 1;
                }
                // if ( $address != null ) $this->_different_upload($this->upload_tmpFileName, $uploaded_filename, $address); // 다른 서버로 파일 업로드 
                @unlink($this->upload_tmpFileName);
            }
        }
    }

    // 다른 서버에 파일 업로드
    //$ftp_url = 'userid:password@example.com'; 
    function _different_upload($temp_file, $upload_file, $adderss) {
        
     if (!empty($temp_file) && !empty($address)) { 
         $ch = curl_init(); 
         $localfile = $temp_file; 
         $fp = fopen($localfile, 'r'); 
         curl_setopt($ch, CURLOPT_URL, 'ftp://'.$ftp_url.'/'.$upload_file); 
         curl_setopt($ch, CURLOPT_UPLOAD, 1); 
         curl_setopt($ch, CURLOPT_INFILE, $fp); 
         curl_setopt($ch, CURLOPT_INFILESIZE,filesize($localfile)); 
         curl_exec ($ch); 
         $error_no = curl_errno($ch); 
         curl_close ($ch); 

            if ($error_no != 0) { 
                //errormsg($adderss." 로 저장하지 못하였습니다.");
            } 
     } else { 
            //errormsg("잘못된 호출입니다.");
     }  
    }
    
    // 이미지정보
    function _getImageSize($tmp_file)
    {
        $img = @getimagesize($tmp_file);

        $img[0] = $img[0] ? $img[0] : 0;
        $img[1] = $img[1] ? $img[1] : 0;

        return $img;
    }

    // 금지 확장자명을 허용 확장자로 변경하여 파일명 지정
    /*
    function _checkFileName($fileName)
    {
        $fileName = strtolower($fileName);

        foreach($this->denied_ext as $key => $value)
        {
            if($this->_getExtension($fileName) == trim($key))
            {
                $expFileName = explode(".", $fileName);

                for($i = 0; $i < sizeof($expFileName) - 1; $i++)
                {
                    $fname .= $expFileName[$i] . $value;
                }
                return $fname;
            }
        }
        return $fileName;
    }
    */

    function _checkFileName($fileName)
    {
        $fileName = strtolower($fileName);
        $temp_file = strstr($fileName,".");

        foreach($this->denied_ext as $key => $value)
        {
               if ( preg_match('/'.trim($key).'/i', $temp_file ) ) {
                    echo "<script type='text/javascript'>\n";
                    echo "<!--\n";
                    echo "alert('첨부 허용된 파일 형태가 아닙니다.');\n";
                    echo "history.back();\n";
                    echo "//-->\n";
                    echo "</script>";
                    exit;
            }
        }
        return $fileName;
    }

    /**
     * 파일명의 빈 부분을 "_" 로 변경
     */
    function _emptyToUnderline($fileName)
    {
        $fileName = str_replace("(", "", $fileName);
        $fileName = str_replace(")", "", $fileName);
        return preg_replace("/\ /i", "_", $fileName);
    }

    /**
     * 중복 파일명 변경
     */
    function _isFilsName($fileName)
    {
    $dtime = date(mdHis);
        if ( is_file($this->upload_directory . "/" . $this->upload_subdirectory . "/" . $fileName) ) {
            $fileName = $dtime."_".$fileName;
        }
        return $fileName;
    }

    /**
     * 한글 파일명 변경
     */ 
     
    function _isHangulName($fileName)
    {
        
            $han = 0;
          $dtime = date(mdHis);
            $fileName_Type = $this->_getExtension($fileName);
            $fileName_Name = str_replace(".".$fileName_Type,"",$fileName);
            $fileName_len = strlen($fileName_Name) - 1;
                $rnd = rand(100, 1000);            
                for($i = 0; $i < strlen($fileName_Name); $i++) {
                   if(ord($fileName_Name[$i]) >= 0x80) {
                        $han = 1;
                        //break;
                   }
                }
                
            if ( $han == 1 ) {
                $fileName = $rnd."".ord($fileName_Name[$fileName_len])."".$dtime.".".$fileName_Type;
            } else $fileName = $fileName_Name."".$rnd.".".$fileName_Type;
                return $fileName;
    }
    
    /**
     * 확장자 추출
     */
    function _getExtension($fileName)
    {
        //return strtolower(substr(strrchr($fileName, "."), 1));
        $path = pathinfo($fileName);
        return $path['extension'];
    }

    /**
     * 이미지 파일이 아닌 파일이 존재한다면 에러
     */
    function checkImageOnly()
    {
        if(is_array($this->upload_tmpFileName))
        {
            for($i = 0; $i < sizeof($this->upload_tmpFileName); $i++)
            {

                if ( !is_null( $this->upload_fileName[$i] )) {

                    if($this->_isThisImageFile($this->upload_fileType[$i]) == false)
                    {
                        errormsg($this->upload_fileName[$i] . " 은 이미지 파일이 아닙니다.");
                    }

                }
            }
        }
        else
        {
            if(is_uploaded_file($this->upload_tmpFileName))
            {
                if($this->_isThisImageFile($this->upload_fileType) == false)
                {
                    errormsg($this->upload_fileName . " 은 이미지 파일이 아닙니다.");
                }
            }
        }
    }

    /*
     * gd library information - array
     * --------------------------------------------------------------------------------
     * GD Version : string value describing the installed libgd version.
     * Freetype Support : boolean value. TRUE if Freetype Support is installed.
     * Freetype Linkage : string value describing the way in which Freetype was linked. Expected values are: 'with freetype',
                          'with TTF library', and 'with unknown library'. This element will only be defined if Freetype Support
                          evaluated to TRUE.
     * T1Lib Support : boolean value. TRUE if T1Lib support is included.
     * GIF Read Support : boolean value. TRUE if support for reading GIF images is included.
     * GIF Create Support : boolean value. TRUE if support for creating GIF images is included.
     * JPG Support : boolean value. TRUE if JPG support is included.
     * PNG Support : boolean value. TRUE if PNG support is included.
     * WBMP Support : boolean value. TRUE if WBMP support is included.
     * XBM Support : boolean value. TRUE if XBM support is included.
     * --------------------------------------------------------------------------------
     */
    function makeThumbnailed($max_width, $max_height, $thumb_head = null)
    {
        if(extension_loaded("gd") == false)
        {
            errormsg("GD 라이브러리가 설치되어 있지 않습니다.");
        }
        else
        {
            $gd = @gd_info();

            if(substr_count(strtolower($gd['GD Version']), "2.") == 0)
            {
                $this->_thumbnailedOldGD($max_width, $max_height, $thumb_head);
            }
            else
            {
                $this->_thumbnailedNewGD($max_width, $max_height, $thumb_head);
            }
        }
    }

    /**
     * GD Library 1.X 버전대를 위한 섬네일 함수
     *
     * GIF 포맷을 지원하지 않음
     */
    function _thumbnailedOldGD($max_width, $max_height, $thumb_head)
    {
        if(is_array($this->upload_tmpFileName))
        {
            $this->_mkUploadDir();
            $this->_mkUploadSubDir();

            for($i = 0; $i < sizeof($this->upload_tmpFileName); $i++)
            {
                if($this->_isThisImageFile($this->upload_fileType[$i]) == true)
                {
                    switch($this->upload_fileImageType[$i])
                    {
                        case 1 :
                            $im = @imagecreatefromgif($this->upload_tmpFileName[$i]);
                        break;
                        case 2 :
                            $im = @imagecreatefromjpeg($this->upload_tmpFileName[$i]);
                        break;
                        case 3 :
                            $im = @imagecreatefrompng($this->upload_tmpFileName[$i]);
                        break;
                    }

                    if(!$im)
                    {
                        errormsg("썸네일 이미지 생성 중 문제가 발생하였습니다.");
                    }
                    else
                    {
                        $sizemin = $this->_getWidthHeight($this->upload_fileWidth[$i], $this->upload_fileHeight[$i], $max_width, $max_height);

                        $small = @imagecreate($sizemin[width], $sizemin[height]);

                        @imagecolorallocate($small, 255, 255, 255);
                        @imagecopyresized($small, $im, 0, 0, 0, 0, $sizemin[width], $sizemin[height], $this->upload_fileWidth[$i], $this->upload_fileHeight[$i]);
                          
                        $thumb_head = ($thumb_head != null) ? $thumb_head : "thumb";
                        
                        /* 이미지 thumb를 확장자 앞으로 이동 수정 */
                        //$thumb_fileName_Name = str_replace(".".$this->upload_fileType[$i],"",$this->upload_fileName[$i]);
                        $thumb_filename = $this->upload_directory . "/" . $this->upload_subdirectory . "/" . "${thumb_head}_" . $this->upload_fileName[$i];
                        /* 이미지 thumb를 확장자 앞으로 이동 수정 */
                        
                        //$thumb_filename = $this->upload_directory . "/" . $this->upload_subdirectory . "/" . $this->upload_fileName[$i] . ".${thumb_head}";

                        if ($this->upload_fileImageType[$i] == 2)
                        {
                            if(@imagejpeg($small, $thumb_filename, 100) == false)
                            {
                                errormsg("jpg/jpeg 썸네일 이미지를 생성하지 못하였습니다.");
                            }
                        }
                        else if ($this->upload_fileImageType[$i] == 3)
                        {
                            if(@imagepng($small, $thumb_filename) == false)
                            {
                                errormsg("png 썸네일 이미지를 생성하지 못하였습니다.");
                            }
                        }

                        if($small != null)
                        {
                            @imagedestroy($small);
                        }
                        if($im != null)
                        {
                            @imagedestroy($im);
                        }
                    }
                }
            }
        }
        else
        {
            if(is_uploaded_file($this->upload_tmpFileName))
            {
                $this->_mkUploadDir();
                $this->_mkUploadSubDir();

                if($this->_isThisImageFile($this->upload_fileType) == true)
                {
                    switch($this->upload_fileImageType)
                    {
                        case 1 :
                            $im = @imagecreatefromgif($this->upload_tmpFileName);
                        break;
                        case 2 :
                            $im = @imagecreatefromjpeg($this->upload_tmpFileName);
                        break;
                        case 3 :
                            $im = @imagecreatefrompng($this->upload_tmpFileName);
                        break;
                    }

                    if(!$im)
                    {
                        errormsg("썸네일 이미지 생성 중 문제가 발생하였습니다.");
                    }
                    else
                    {
                        $sizemin = $this->_getWidthHeight($this->upload_fileWidth, $this->upload_fileHeight, $max_width, $max_height);

                        $small = @imagecreate($sizemin[width], $sizemin[height]);

                        @imagecolorallocate($small, 255, 255, 255);
                        @imagecopyresized($small, $im, 0, 0, 0, 0, $sizemin[width], $sizemin[height], $this->upload_fileWidth, $this->upload_fileHeight);

                        $thumb_head = ($thumb_head != null) ? $thumb_head : "thumb";
                        
                        /* 이미지 thumb를 확장자 앞으로 이동 수정 */
                        //$thumb_fileName_Name = str_replace(".".$this->upload_fileType,"",$this->upload_fileName);
                        $thumb_filename = $this->upload_directory . "/" . $this->upload_subdirectory . "/" . "${thumb_head}_" . $this->upload_fileName;
                        /* 이미지 thumb를 확장자 앞으로 이동 수정 */
                                                
                        //$thumb_filename = $this->upload_directory . "/" . $this->upload_subdirectory . "/" . $this->upload_fileName . ".${thumb_head}";

                        if ($this->upload_fileImageType == 2)
                        {
                            if(@imagejpeg($small, $thumb_filename, 100) == false)
                            {
                                errormsg("jpg/jpeg 썸네일 이미지를 생성하지 못하였습니다.");
                            }
                        }
                        else if ($this->upload_fileImageType == 3)
                        {
                            if(@imagepng($small, $thumb_filename) == false)
                            {
                                errormsg("png 썸네일 이미지를 생성하지 못하였습니다.");
                            }
                        }

                        if($small != null)
                        {
                            @imagedestroy($small);
                        }
                        if($im != null)
                        {
                            @imagedestroy($im);
                        }
                    }
                }
            }
        }
    }

    /**
     * GD Library 2.X 버전대를 위한 섬네일 함수
     *
     * GIF 포맷을 지원함
     */
    function _thumbnailedNewGD($max_width, $max_height,  $thumb_head)
    {
        if(is_array($this->upload_tmpFileName))
        {
            $this->_mkUploadDir();
            $this->_mkUploadSubDir();

            for($i = 0; $i < sizeof($this->upload_tmpFileName); $i++)
            {
                if($this->_isThisImageFile($this->upload_fileType[$i]) == true)
                {
                    switch($this->upload_fileImageType[$i])
                    {
                        case 1 :
                            $im = @imagecreatefromgif($this->upload_tmpFileName[$i]);
                        break;
                        case 2 :
                            $im = @imagecreatefromjpeg($this->upload_tmpFileName[$i]);
                        break;
                        case 3 :
                            $im = @imagecreatefrompng($this->upload_tmpFileName[$i]);
                        break;
                    }

                    $sizemin = $this->_getWidthHeight($this->upload_fileWidth[$i], $this->upload_fileHeight[$i], $max_width, $max_height);

                    $small = @imagecreatetruecolor($sizemin[width], $sizemin[height]);

                    @imagecolorallocate($small, 255, 255, 255);
                    @imagecopyresampled($small, $im, 0, 0, 0, 0, $sizemin[width], $sizemin[height], $this->upload_fileWidth[$i], $this->upload_fileHeight[$i]);

                    $thumb_head = ($thumb_head != null) ? $thumb_head : "thumb";

                    /* 이미지 thumb를 확장자 앞으로 이동 수정 */
                   // $thumb_fileName_Name = str_replace(".".$this->upload_fileType[$i],"",$this->upload_fileName[$i]);
                    $thumb_filename = $this->upload_directory . "/" . $this->upload_subdirectory . "/" . "${thumb_head}_" . $this->upload_fileName[$i];
                    /* 이미지 thumb를 확장자 앞으로 이동 수정 */ 
                    
                    //$thumb_filename = $this->upload_directory . "/" . $this->upload_subdirectory . "/" . $this->upload_fileName[$i] . ".${thumb_head}";

                    if($this->upload_fileImageType[$i] == 1)
                    {
                        if(@imagegif($small, $thumb_filename) == false)
                        {
                            errormsg("gif 썸네일 이미지를 생성하지 못하였습니다.");
                        }
                    }
                    else if ($this->upload_fileImageType[$i] == 2)
                    {
                        if(@imagejpeg($small, $thumb_filename, 100) == false)
                        {
                            errormsg("jpg/jpeg 썸네일 이미지를 생성하지 못하였습니다.");
                        }
                    }
                    else if ($this->upload_fileImageType[$i] == 3)
                    {
                        if(imagepng($small, $thumb_filename) == false)
                        {
                            errormsg("png 썸네일 이미지를 생성하지 못하였습니다.");
                        }
                    }

                    if($small != null)
                    {
                        @imagedestroy($small);
                    }
                    if($im != null)
                    {
                        @imagedestroy($im);
                    }
                }
            }
        }
        else
        {
            if(is_uploaded_file($this->upload_tmpFileName))
            {
                $this->_mkUploadDir();
                $this->_mkUploadSubDir();

                if($this->_isThisImageFile($this->upload_fileType) == true)
                {
                    switch($this->upload_fileImageType)
                    {
                        case 1 :
                            $im = @imagecreatefromgif($this->upload_tmpFileName);
                        break;
                        case 2 :
                            $im = @imagecreatefromjpeg($this->upload_tmpFileName);
                        break;
                        case 3 :
                            $im = @imagecreatefrompng($this->upload_tmpFileName);
                        break;
                    }

                    $sizemin = $this->_getWidthHeight($this->upload_fileWidth, $this->upload_fileHeight, $max_width, $max_height);

                    $small = @imagecreatetruecolor($sizemin[width], $sizemin[height]);

                    @imagecolorallocate($small, 255, 255, 255);
                    @imagecopyresampled($small, $im, 0, 0, 0, 0, $sizemin[width], $sizemin[height], $this->upload_fileWidth, $this->upload_fileHeight);

                    // 비율에 맞게 만들어진 여분을 색으로 채움..... 화질과 배경색이 이상함...!!!! 미완성
                    /*
                          $left =( $max_width - $sizemin[width] )/ 2;
                          $top = ( $max_height - $sizemin[height]) / 2;

                          $fillimg = imagecreatetruecolor($max_width,$max_height);

                          $black = ImageColorAllocate($small,255,255,255);
                          @ImageRectangle($small,0,0,$sizemin[width]-1,$sizemin[height]-1,$black);

                          ImageCopy($fillimg,$small,$left,$top,0,0,$max_width,$max_height);

                          $small = $fillimg;
                    */
                    // ============================================================================

                    $thumb_head = ($thumb_head != null) ? $thumb_head : "thumb";
                    $thumb_filename = $this->upload_directory . "/" . $this->upload_subdirectory . "/" . "${thumb_head}_" . $this->upload_fileName;                    
                    //$thumb_filename = $this->upload_directory . "/" . $this->upload_subdirectory . "/" . $this->upload_fileName . ".${thumb_head}";

                    if($this->upload_fileImageType == 1)
                    {
                        if(@imagegif($small, $thumb_filename) == false)
                        {
                            errormsg("gif 썸네일 이미지를 생성하지 못하였습니다.");
                        }
                    }
                    else if ($this->upload_fileImageType == 2)
                    {
                        if(@imagejpeg($small, $thumb_filename, 100) == false)
                        {
                            errormsg("jpg/jpeg 썸네일 이미지를 생성하지 못하였습니다.");
                        }
                    }
                    else if ($this->upload_fileImageType == 3)
                    {
                        if(imagepng($small, $thumb_filename) == false)
                        {
                            errormsg("png 썸네일 이미지를 생성하지 못하였습니다.");
                        }
                    }

                    if($small != null)
                    {
                        @imagedestroy($small);
                    }
                    if($im != null)
                    {
                        @imagedestroy($im);
                    }
                }
            }
        }
    }

    /**
     * 제한된 가로/세로 길이에 맞추어 원본이미지의 줄인 windth, height 값을 리턴
     * _getWidthHeight(원본이미지가로, 원본이미지세로, 제한가로, 제한세로)
     */

    function _getWidthHeight($org_width, $org_height, $max_width, $max_height)
    {
        $img = array();

        if($org_width <= $max_width && $org_height <= $max_height)
        {
            $img[width] = $org_width;
            $img[height] = $org_height;
        }
        else
        {
            if($org_width > $org_height)
            {
                $img[width] = $max_width;
                $img[height] = ceil($org_height * $max_width / $org_width);
            }
            else if($org_width < $org_height)
            {
                $img[width] = ceil($org_width * $max_height / $org_height);
                $img[height] = $max_height;
            }
            else
            {
                $img[width] = $max_width;
                $img[height] = $max_height;
            }

            if($img[width] > $max_width)
            {
                $img[width] = $max_width;
                $img[height] = ceil($org_height * $max_width / $org_width);
            }
            if($img[height] > $max_height)
            {
                $img[width] = ceil($org_width * $max_height / $org_height);
                $img[height] = $max_height;
            }
        }
        return $img;
    }
    
    function __destruct()
    {
    }
        
}
?>