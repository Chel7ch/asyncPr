<?php

namespace DB;

class DBPdoCRUD
{
    public  $sql;

    public function __construct(\DB\IDBConnection $pdo)
    {
        $this->sql = $pdo;
    }

    public function connect()
    {
        return $this->sql->connect();
    }

    public function insertDB($sql=''){
        $this->connect()->exec($sql);
    }

    public function selDBProxy($tab ='collect_proxy')
    {
        $sql = 'SELECT * FROM '. $tab  ;
//        $sql = 'SELECT COUNT(*) FROM collect_proxy';
//        $sql = 'SELECT field1 FROM ' . $tab . ' WHERE field2 = "HTTP" ORDER BY id DESC LIMIT 0,20;';

        $st = $this->connect()->query($sql);
        $results = $st->fetchAll();
//        print_r($results);
        return $results;
//        foreach ($results as $row) {
////            print_r($results);
//            echo $row['id'] . ' ';
//            echo $row['field1'] . ' ';
//            echo $row['field2'] . ' ';
//            echo $row['field4'].' <br>';
////            echo $row['field3'].'<br> ';
//        }
    }

    public function selectDB($sql='')
    {
        $sql = 'SELECT * FROM '. TAB_NAME;

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

    public function cleanTable($nameTable){
        $sql = "TRUNCATE $nameTable";
        $this->connect()->exec($sql);
    }
}