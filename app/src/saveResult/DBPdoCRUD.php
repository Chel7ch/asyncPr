<?php

namespace DB;

use PDO;

class DBPdoCRUD
{
    public $sql;

    public function __construct(\DB\IDBConnection $pdo)
    {
        $this->sql = $pdo;
    }

    public function connect()
    {
        return $this->sql->connect();
    }

    public function insertDB($sql = '')
    {
        $this->connect()->exec($sql);
    }

    public function selDBProxy($rowId, $tab = 'collect_proxy')
    {
        static $i = 1;
//        $sql = 'SELECT id, field1 FROM ' . $tab . ' WHERE id >=' . $i . ' AND id <=' . $i = $i + 50;
        $sql = 'SELECT  field1 FROM ' . $tab . ' WHERE id >' . $rowId . ' LIMIT 50';
        $results = $this->connect()->query($sql)->fetchAll(PDO::FETCH_COLUMN);

        echo $rowId . 'table<br>';

        return $results;
    }

    public function selectDB($sql = '')
    {
        $sql = 'SELECT * FROM ' . TAB_NAME;

        $st = $this->connect()->query($sql);
        $results = $st->fetchAll();

        foreach ($results as $row) {
            echo $row['id'] . ' ';
            echo $row['links'] . ' ';
            echo $row['field1'] . '<br> ';
//            echo $row['field2'].' ';
//            echo $row['field3'].'<br> ';
        }
    }

    public function cleanTable($nameTable)
    {
        $sql = "TRUNCATE $nameTable";
        $this->connect()->exec($sql);
    }
}