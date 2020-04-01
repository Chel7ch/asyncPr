<?php

namespace DB;

use PDO;

class DBPdoExecute
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

    public function execInsert($sql)
    {
        $this->connect()->exec($sql);
    }

    public function execSelect($sql)
    {
        $results = $this->connect()->query($sql)->fetchAll(PDO::FETCH_COLUMN);

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