<?php

echo date('Y/m/d H:i:s').' START '.$_SERVER['PHP_SELF'].'\n';

require APP_ROOT_DIR . '/dao/DbConnection.php';
require APP_ROOT_DIR . '/helper/CrawlerHelper.php';
require APP_ROOT_DIR . '/dao/CommonDao.php';
require APP_ROOT_DIR . '/dao/TCountDao.php';

$helper = new CrawlerHelper();

//SELECTリスト生成
$conn = new DbConnection();
$dbh = $conn->getConnection();

$mSiteDao = new CommonDao($dbh, 'M_SITE');
$mSiteList = $mSiteDao->getAllData();

$mCateDao = new CommonDao($dbh, 'M_CATEGORY');
$mCateList = $mCateDao->getAllData();

$mWordDao = new CommonDao($dbh, 'M_WORD');
$mWordList = $mWordDao->getAllData();

//件数の更新
$successCnt = 0;
$failedCnt = 0;
for($i = 0; $i < count($mWordList); $i++){
    $word = $mWordList[$i];

    for($j = 0; $j < count($mSiteList); $j++){
        $site = $mSiteList[$j];
        $fileData = $helper::getSiteinfo($site['SITE_NM'], $site['METHOD'], $site['URL'], $word['WORD'], $site['POST_WORD'], '' );
        //echo $site['CNT_REPLACE_STRING'] . '\n';
        //echo mb_convert_encoding($site['CNT_REPLACE_STRING'],'UTF-8','UTF-8') . '\n';
        $cnt = $helper::getCountByMatch($fileData, $site['CNT_REPLACE_STRING']);
        $cnt = ( $cnt == '' ) ? -1 : $cnt;

        $dataList = array();
        $dataList += array('DATE'=> date('Ymd'));
        $dataList += array('WORD_ID'=>$word['ID']);
        $dataList += array('SITE_ID'=>$site['ID']);
        $dataList += array('COUNT'=>$cnt);
        $tCountDao = new TCountDao($dbh);
        if($tCountDao->insert($dataList)){
            $successCnt++;
        }else{
            $failedCnt++;
        }
    }
}
echo 'InsertedCount:'.$successCnt.' FailedCount:'.$failedCnt.'\n';
echo date('Y/m/d H:i:s').' END '.$_SERVER['PHP_SELF'].'\n';

