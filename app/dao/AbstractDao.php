<?php
abstract class AbstractDao{

    protected $dbh;
    protected $tableName;
    protected $columnList = array();

    public function __construct($dbh) {
        $this->dbh = $dbh;
        if(isset($this->columnList)){
            $this->columnList = self::getTableInfo();
        }
    }

    public function getAllData($delFlg, $order) {
        try{
            if($delFlg == TRUE){
                $stmt = $this->dbh->prepare('SELECT * FROM ' . $this->tableName . ' WHERE DELETE_FLG = FALSE ' . $order . ';');
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

    protected function getDataByCondLast($stmt, $param) {

        $j = 1;
        for($i = 0 ; $i < count($param) ; $i++){
//echo $j . '/' . $param[$i]['Value'].'<br>\n';
            $stmt->bindParam($j, $param[$i]['Value'], $param[$i]['Type']);
            $j++;
        }

        $stmt->execute();
     
        $dataList = array();
        while ($row = $stmt->fetch()) {
            $data = self::getRowData($row);
            $dataList[] = $data;
        }
        return $dataList;
    }

    protected function getTableInfo() {
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
        } catch(PDOException $e){
            echo 'Desc failed: ' . $e->getMessage().'\n';
        }
        return $columnList;
    }
         
    protected function getRowData($row) {
        $rowData = array();
        for($i = 0; $i < count($this->columnList); $i++){
//echo $i . '|' . $this->columnList[$i]['Field'] . ' = ' . $row[$this->columnList[$i]['Field']]. '<br>\n';
            $rowData[$this->columnList[$i]['Field']] = $row[$this->columnList[$i]['Field']];
        }
        return $rowData;
    }

    public function insert($data) {

        $this->columnList = self::getTableInfo();
        $sql = 'INSERT INTO ' . $this->tableName . ' VALUES(';
        for($i = 0; $i < count($this->columnList); $i++){

            if(strpos($this->columnList[$i]['Type'], '.varchar') > 0){
                $sql .= "'" . $data[$this->columnList[$i]['Field']] . "'";
            }else{
                $sql .= $data[$this->columnList[$i]['Field']];
            }
            if($i == count($this->columnList) - 1 ){
                $sql .=  ');';
            }else{
                $sql .=  ',';
            }
        }
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        } catch (PDOException $e) {
            echo 'Insert failed: ' . $e->getMessage().'\n';
            echo 'Insert SQL=' . $sql .'\n';
            return false;
        }
        return true;
    }

}
