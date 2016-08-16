<?php
require APP_ROOT_DIR . '/dao/AbstractDao.php';

class CommonDao extends AbstractDao {

    public function __construct($dbh, $tableName) {
        $this->tableName = $tableName;
        parent::__construct($dbh);
    }

}
