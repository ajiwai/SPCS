<?php
class MSiteDao extends AbstractDao {

    public function __construct($dbh) {
        $this->tableName = 'M_SITE';
        parent::__construct($dbh);
    }

    public function getAllData() {
        try{

            $stmt = $this->dbh->prepare('SELECT *, SITE_NM' || '(' || TYPE || ') AS SITE_NM_DISP FROM ' . $this->tableName . ' WHERE DELETE_FLG = FALSE;');
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $stmt->execute();
         
            $dataList= array();
            while ($row = $stmt->fetch()) {
                $data = self::getRowData($row);
                $dataList[] = $data;
            }
         
            return $dataList;
         
        } catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    protected function getTableInfo() {

        try{
            $columnList = parent::getTableInfo();
            $dataList = array();
            $dataList += array('Field'=> 'SITE_NM_DISP');
            $columnList[count($columnList) + 1] = $dataList;

        } catch(Exception $e){
            echo $e->getMessage();
        }
        return $columnList;
    }


}
