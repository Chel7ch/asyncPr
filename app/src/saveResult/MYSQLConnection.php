<?php

namespace DB;

use PDO;
use PDOException;

class MYSQLConnection implements IDBConnection
{

    private $pdo = null;

    public function connect()
    {
        $this->createDB();

        try {
            if ($this->pdo == null) {
                $dsn = 'mysql:host=' . DBConfig::HOST . '; dbname=' . DBConfig::DBASE . ';charset=' . DBConfig::CHARSET;
                $opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,];
                $this->pdo = new PDO($dsn, DBConfig::USER, DBConfig::PASS, $opt);

            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $this->pdo;
    }

    public function createDB(){
        try {
            $dsn = 'mysql:host=' . DBConfig::HOST .'; charset=' . DBConfig::CHARSET;
            $this->pdo = new PDO($dsn, DBConfig::USER, DBConfig::PASS);

            $sql = 'CREATE DATABASE IF NOT EXISTS ' . DBConfig::DBASE;
            $this->pdo->exec($sql);

            if ($this->pdo == null)
                throw new \PDOException('There are not database!');

        } catch (PDOException $e)
        {
            echo $sql . "<br>" . $e->getMessage();
        }
        $this->pdo = null;
    }

}