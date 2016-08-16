<?php
class TCountDao extends AbstractDao {

    public function __construct($dbh) {
        $this->tableName = 'T_COUNT';
        parent::__construct($dbh);
            $this->columnList = parent::getTableInfo();
    }

    public function getDataByCond($date, $cateId, $wordId, $freeWord) {
        try{

            $this->columnList = self::getTableInfoExtra();

            $where = '';
            $i = 0;
            $param = array();

            if ($date != '') {
                $where .= ($where == '' ) ? 'WHERE TC.DATE = ? ' : 'AND TC.DATE = ? ';
                $param[$i] =  array('Value'=>$date, 'Type'=>PDO::PARAM_STR );
                 $i++;
            }
            if ($wordId != '') {
                $where .= ($where == '' ) ? 'WHERE TC.WORD_ID = ? ' : 'AND TC.WORD_ID = ? ';
                $param[$i] =  array('Value'=>$wordId, 'Type'=>PDO::PARAM_INT );
                $i++;
            }
            if ($cateId != '') {
                $where .= ($where == '' ) ? 'WHERE MW.CATEGORY_ID = ? ' : 'AND MW.CATEGORY_ID = ? ';
                $param[$i] =  array('Value'=>$cateId, 'Type'=>PDO::PARAM_INT );
                $i++;
            }
            if ($freeWord != '') {
                $where .= ($where == '' ) ? 'WHERE MW.WORD LIKE ? ' : 'AND MW.WORD LIKE ? ';
                $param[$i] =  array('Value'=>'%'.$freeWord.'%', 'Type'=>PDO::PARAM_STR );
                $i++;
            }
            $stmt = $this->dbh->prepare('SELECT TC.*,FORMAT(TC.COUNT,0) AS DISP_COUNT, MW.WORD, MW.TAGS, MC.CATEGORY_NM, MS.TYPE SITE_TYPE FROM ' . $this->tableName . 
                    ' TC LEFT OUTER JOIN M_WORD MW ON TC.WORD_ID = MW.ID ' .
                    'LEFT OUTER JOIN M_SITE MS ON TC.SITE_ID = MS.ID ' . 
                    'LEFT OUTER JOIN M_CATEGORY MC ON MW.CATEGORY_ID = MC.ID ' . $where . ' ORDER BY TC.WORD_ID,TC.DATE');
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
         
            return parent::getDataByCondLast($stmt, $param);
         
        } catch(PDOException $e){
            echo 'Select failed: ' . $e->getMessage();
        } catch(Exception $e){
            echo 'Other failed: ' . $e->getMessage();
        }
    }

    public function getMonthlyDataByCond($siteId, $cateId, $wordId, $freeWord) {
        try{

            $this->columnList = self::getTableInfoExtra();

            $where = '';
            $i = 0;
            $param = array();

            if ($siteId != '') {
                $where .= ($where == '' ) ? 'WHERE SITE_ID = ? ' : 'AND SITE_ID = ? ';
                $param[$i] =  array('Value'=>$siteId, 'Type'=>PDO::PARAM_INT );
                $i++;
            }
            if ($cateId != '') {
                $where .= ($where == '' ) ? 'WHERE CATEGORY_ID = ? ' : 'AND CATEGORY_ID = ? ';
                $param[$i] =  array('Value'=>$cateId, 'Type'=>PDO::PARAM_INT );
                $i++;
            }
            if ($wordId != '') {
                $where .= ($where == '' ) ? 'WHERE WORD_ID = ? ' : 'AND WORD_ID = ? ';
                $param[$i] =  array('Value'=>$wordId, 'Type'=>PDO::PARAM_INT );
                $i++;
            }
            if ($freeWord != '') {
                $where .= ($where == '' ) ? 'WHERE WORD LIKE ? ' : 'AND WORD LIKE ? ';
                $param[$i] =  array('Value'=>'%'.$freeWord.'%', 'Type'=>PDO::PARAM_STR );
                $i++;
            }

            $stmt = $this->dbh->prepare("SELECT DATE_FORMAT(DATE, '%Y%m') AS YYYYMM,FORMAT(AVG(COUNT),0) AS DISP_COUNT,AVG(COUNT) AS AVG_COUNT,WORD_ID,SITE_ID,WORD,TAGS,CATEGORY_ID,CATEGORY_NM,SITE_NM,SITE_TYPE FROM " .
                    ' (SELECT TC.*, MW.WORD, MW.TAGS, MW.CATEGORY_ID, MC.CATEGORY_NM, MS.SITE_NM, MS.TYPE SITE_TYPE FROM T_COUNT ' .
                    '  TC LEFT OUTER JOIN M_WORD MW ON TC.WORD_ID = MW.ID ' .
                    ' LEFT OUTER JOIN M_SITE MS ON TC.SITE_ID = MS.ID ' .
                    ' LEFT OUTER JOIN M_CATEGORY MC ON MW.CATEGORY_ID = MC.ID ) T ' . $where .
                    " GROUP BY DATE_FORMAT(DATE, '%Y%m'),WORD_ID,SITE_ID,WORD,TAGS,CATEGORY_ID, CATEGORY_NM,SITE_NM,SITE_TYPE ORDER BY WORD_ID,SITE_ID,DATE_FORMAT(DATE, '%Y%m')");
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
         
            return parent::getDataByCondLast($stmt, $param);
         
        } catch(PDOException $e){
            echo 'Select failed: ' . $e->getMessage();
        } catch(Exception $e){
            echo 'Other failed: ' . $e->getMessage();
        }
    }


    public function getMonthlyData($delFlg, $order, $fromYyyymm, $toYyyymm) {
        try{
            if($delFlg == TRUE){
                $stmt = $this->dbh->prepare('SELECT * FROM ' . $this->tableName . " WHERE DELETE_FLG = FALSE AND DATE_FORMAT(DATE, '%Y%m') BETWEEN " . $fromYyyymm . ' AND ' . $toYyyymm . ' ' . $order . ';');
            }else{
                $stmt = $this->dbh->prepare('SELECT * FROM ' . $this->tableName . ' ' . $order . ';');
            }
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $stmt->execute();
         
            $dataList= array();
            while ($row = $stmt->fetch()) {
                $data = self::getRowData($row);
                $dataList[] = $data;
            }
         
            return $dataList;
         
        } catch(PDOException $e){
            echo 'Select failed: ' . $e->getMessage().'\n';
        }
    }

    public function getMonthList() {

        try{
            $this->columnList = self::getTableInfoExtra();

            $stmt = $this->dbh->prepare("SELECT DATE_FORMAT(DATE, '%Y%m') AS YYYYMM FROM T_COUNT GROUP BY DATE_FORMAT(DATE, '%Y%m') ORDER BY DATE_FORMAT(DATE, '%Y%m')");

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
         
            $dataList= array();
            while ($row = $stmt->fetch()) {
                $data = self::getRowData($row);
                $dataList[] = $data;
            }
            return $dataList;
         
        } catch(PDOException $e){
            echo 'Select failed: ' . $e->getMessage();
        } catch(Exception $e){
            echo 'Other failed: ' . $e->getMessage();
        }
    }

    protected function getTableInfoExtra() {
        try{

            $stmt = $this->dbh->prepare('desc ' . $this->tableName . ';');
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $stmt->execute();
         
            $columnList = array();
            $i = 0;
            while ($row = $stmt->fetch()) {
                $dataList = array();
                $dataList += array('Field'=>$row['Field']);
                $dataList += array('Type'=>$row['Type']);
                $dataList += array('Null'=>$row['Null']);
                $dataList += array('Key'=>$row['Key']);
                $dataList += array('Default'=>$row['Default']);
                $dataList += array('Extra'=>$row['Extra']);
                $columnList[$i] = $dataList;
                $i++;
            }
            $dataList = array('Field'=>'WORD');
            $columnList[$i] = $dataList;
            $i++;
            $dataList = array('Field'=>'CATEGORY_NM');
            $columnList[$i] = $dataList;
            $i++;
            $dataList = array('Field'=>'TAGS');
            $columnList[$i] = $dataList;
            $i++;
            $dataList = array('Field'=>'SITE_TYPE');
            $columnList[$i] = $dataList;
            $i++;
            $dataList = array('Field'=>'SITE_NM');
            $columnList[$i] = $dataList;
            $i++;
            $dataList = array('Field'=>'YYYYMM');
            $columnList[$i] = $dataList;
            $i++;
            $dataList = array('Field'=>'AVG_COUNT');
            $columnList[$i] = $dataList;
            $i++;
            $dataList = array('Field'=>'DISP_COUNT');
            $columnList[$i] = $dataList;
            $i++;
        } catch(PDOException $e){
            echo 'Desc failed: ' . $e->getMessage();
        }
        return $columnList;
    }
}
