<?php
require APP_ROOT_DIR . '/dao/DbConnection.php';
//require APP_ROOT_DIR . '/dao/AbstractDao.php';
require APP_ROOT_DIR . '/dao/CommonDao.php';
require APP_ROOT_DIR . '/dao/MSiteDao.php';
require APP_ROOT_DIR . '/dao/TCountDao.php';
require APP_ROOT_DIR . '/dao/TMonthlyCountDao.php';

class CountListController {

      public function __construct() {
          $this->className = str_replace(CONTROLLER_BASE_NAME,'' ,basename(__FILE__));
      }

    public function view($postData, $functionId) {

        //検索条件の取得
        $searchDate = (isset($postData['search_date'])) ? $postData['search_date'] : date('Ymd');
        $today = date('Ymd');
        $searchSelSiteId = (isset($postData['search_sel_site'])) ? $postData['search_sel_site'] : 0;
        $searchSelCateId = (isset($postData['search_sel_cate'])) ? $postData['search_sel_cate'] : 0;
        $searchSelWordId = (isset($postData['search_sel_word'])) ? $postData['search_sel_word'] : 0;
        $searchFreeWord = (isset($postData['search_word'])) ? $postData['search_word'] : '';
        $chkDisplay = (isset($postData['chk_display'])) ? 'checked' : '';
        $chkDisplay2 = (isset($postData['chk_display2'])) ? 'checked' : '';
        $chkDisplay3 = (isset($postData['chk_display3'])) ? 'checked' : '';

        //プルダウンリスト用データ取得
        $conn = new DbConnection();
        $dbh = $conn->getConnection();
        $mSiteDao = new CommonDao($dbh, 'M_SITE');
        $mSiteList = $mSiteDao->getAllData();
        $mCateDao = new CommonDao($dbh, 'M_CATEGORY');
        $mCateList = $mCateDao->getAllData();
        $mWordDao = new CommonDao($dbh, 'M_WORD');
        $mWordList = $mWordDao->getAllData();

        switch($functionId) {
        case 1:
        case 2:
            switch($functionId) {
            case 1:
                $tCountDao = new TCountDao($dbh);
                //結果TABLE用データ取得
                $tCountDao = new TCountDao($dbh);
                $tCountList = $tCountDao->getDataByCond($searchDate, $searchSelCateId, $searchSelWordId, $searchFreeWord);
                break;
            case 2:
                $tCountDao = new TMonthlyCountDao($dbh);
                //結果TABLE用データ取得
                $tCountList = $tCountDao->getMonthlyDataByCond($searchSelSiteId, $searchSelCateId, $searchSelWordId, $searchFreeWord);
                $monthList = $tCountDao->getMonthList();
                break;
            }

            //画面表示
            if ($functionId == 1) {
                require APP_ROOT_DIR . DS . 'view' . DS . $this->className . '.tmpl';
            } else {
                require APP_ROOT_DIR . DS . 'view' . DS . $this->className . '2.tmpl';
            }
            break;
        default:
            //結果TABLE用データ取得
            $tCountDao = new TCountDao($dbh);
            $tCountList = $tCountDao->getDataByCond($searchDate, $searchSelCateId, $searchSelWordId, $searchFreeWord);

            //画面表示
            require APP_ROOT_DIR . DS . 'view' . DS . $this->className . '.tmpl';
            break;
        }

    }
}


