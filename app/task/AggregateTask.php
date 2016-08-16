<?php

echo date('Y/m/d H:i:s').' START '.$_SERVER['PHP_SELF'].'\n';

require_once APP_ROOT_DIR . '/init.php';
require APP_ROOT_DIR . '/dao/DbConnection.php';
require APP_ROOT_DIR . '/dao/CommonDao.php';
require APP_ROOT_DIR . '/dao/TCountDao.php';
//require APP_ROOT_DIR . '/dao/TMonthlyCountDao.php';

//SELECTリスト生成
$conn = new DbConnection();
$dbh = $conn->getConnection();

$tMonthlyCountDao = new CommonDao($dbh, 'T_MONTHLY_COUNT');

$tCountDao = new TCountDao($dbh);
$tCountList = $tCountDao->getMonthlyData(FALSE, 'ORDER BY WORD_ID,SITE_ID,DATE', $fromYyyymm, $toYyyymm);

//件数の更新
$successCnt = 0;
$failedCnt = 0;
$wkYyyymm = '';
$wkSiteId = '';
$wkWordId = '';
$days = 0;
$countDays = 0;
$totalCount = 0;
$maxCount = 0;
$minCount = 99999999;
for($i = 0; $i < count($tCountList); $i++){
    $countData = $tCountList[$i];
    $yyyymm = substr($countData['DATE'],0,4) . substr($countData['DATE'],5,2);
    $siteId = $countData['SITE_ID'];
    $wordId = $countData['WORD_ID'];
    $count = $countData['COUNT'];

    if(($yyyymm >= $fromYyyymm && $yyyymm <= $toYyyymm) && (($wkWordId != $wordId && $wkWordId != '') || 
        ($wkSiteId != $siteId && $wkSiteId != '') || ($wkYyyymm != $yyyymm && $wkYyyymm != ''))){
        if($countDays == 0 ){
            $avgCount = -1;
        }else{
            $avgCount = $totalCount / $countDays;
        }
        $dataList = array();
        $dataList += array('YYYYMM'=> $wkYyyymm);
        $dataList += array('WORD_ID'=>$wkWordId);
        $dataList += array('SITE_ID'=>$wkSiteId);
        $dataList += array('AVG_COUNT'=>$avgCount);
        $dataList += array('MAX_COUNT'=>$maxCount);
        $dataList += array('MIN_COUNT'=>$minCount);
        $dataList += array('TOTAL_COUNT'=>$totalCount);
        $dataList += array('COUNT_DAYS'=>$countDays);
        $dataList += array('DAYS'=>$days);
        if($tMonthlyCountDao->insert($dataList)){
            $successCnt++;
        }else{
echo $wkYyyymm . '/' . $wkWordId . '/' . $wkSiteId . '/' . $avgCount . '/' . $totalCount . '/' . $countDays . '/' . $maxCount . '/' . $minCount . '\n';
            $failedCnt++;
        }
        $days = 1;
        if($count != -1){
            $countDays = 1;
            $totalCount = $countData['COUNT'];
        }else{
            $countDays = 0;
            $totalCount = 0;
        }
        if($countData['COUNT'] >= 0){
            $maxCount = $countData['COUNT'];
            $minCount = $countData['COUNT'];
	    }else{
	        $maxCount = -2;
	        $minCount = -2;
        }
    }else{
        $days++;
        if($count != -1){
            $countDays++;
            $totalCount += $count;
        }
        if($count >= 0 && $count > $maxCount){
            $maxCount = $count;
        }
        if($count >= 0 && $count < $minCount){
            $minCount = $count;
        }
        if($maxCount == -2 && $count >= 0){
            $maxCount = $count;
        }
        if($minCount == -2 && $count >= 0){
            $minCount = $count;
        }
    }
    $wkYyyymm = $yyyymm;
    $wkSiteId = $siteId;
    $wkWordId = $wordId;
}
if($successCnt > 0 || $failedCnt > 0){
    if($countDays == 0 ){
        $avgCount = -1;
    }else{
        $avgCount = $totalCount / $countDays;
    }
    $dataList = array();
    $dataList += array('YYYYMM'=> $wkYyyymm);
    $dataList += array('WORD_ID'=>$wkWordId);
    $dataList += array('SITE_ID'=>$wkSiteId);
    $dataList += array('AVG_COUNT'=>$avgCount);
    $dataList += array('MAX_COUNT'=>$maxCount);
    $dataList += array('MIN_COUNT'=>$minCount);
    $dataList += array('TOTAL_COUNT'=>$totalCount);
    $dataList += array('COUNT_DAYS'=>$countDays);
    $dataList += array('DAYS'=>$days);
    if($tMonthlyCountDao->insert($dataList)){
        $successCnt++;
    }else{
        $failedCnt++;
    }
}

echo 'InsertedCount:'.$successCnt.' FailedCount:'.$failedCnt.'\n';
echo date('Y/m/d H:i:s').' END '.$_SERVER['PHP_SELF'].'\n';

