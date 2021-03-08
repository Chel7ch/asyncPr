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
        return $this->connect()->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }

    public function cleanTable($nameTable)
    {
        $sql = "TRUNCATE $nameTable";
        $this->connect()->exec($sql);
    }
}