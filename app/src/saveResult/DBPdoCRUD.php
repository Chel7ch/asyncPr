<?php

namespace DB;

class DBPdoCRUD
{
    public  $sql;

//    public function __construct($pdo)
//    {
//        $this->sql = $pdo;
//    }

    public function DBase(IDBConnection $sql)
    {
        $this->sql = $sql;
    }

    public function conn()
    {
        return $this->sql->connect();
    }


    public function prepareInsert($benefit = [])
    {
        $tab = 'INSERT INTO ' . TAB_NAME . '(links,';
        $val = ' VALUES';
        for ($i = 0; $i < TAB_FIELDS; $i++)
            $tab .= 'field' . ($i + 1) . ',';

        for ($i = 0; $i < count($benefit); $i++)
            $val .= '(' . $benefit[$i] . '),';

        $tab = substr($tab, 0, -1);
        $val = substr($val, 0, -1);
        $query = $tab . ')' . $val . ';';

        return $query;
    }

    public function insertDB($sql=''){
        echo '___________________ $sql______________<br>';
        print_r($sql);
//        $sql = "INSERT INTO bk55_ru(links,field1,field2,field3) VALUES('bk55.ru', '15', '18', '14'),('bk55.ru', '60', '33', '180'),('bk55.ru', '25', '516', '7')";
//        $this->connect()->exec($sql);

// echo $query;
    }

    public function selectDB($sql='')
    {
        $sql = 'SELECT * FROM '. TAB_NAME;

        $st = $this->conn()->query($sql);
        $results = $st->fetchAll();


        foreach ($results as $row) {
//            print_r( $row );
            echo $row['id'] . ' ';
            echo $row['links'] . ' ';
            echo $row['field1'] . '<br> ';
//            echo $row['field2'].' ';
//            echo $row['field3'].'<br> ';
        }
    }

}