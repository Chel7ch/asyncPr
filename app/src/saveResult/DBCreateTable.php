<?php

namespace DB;


class DBCreateTable
{

    private $pdo;

    public function DBase(IDBConnection $sql)
    {
        $this->sql = $sql;
    }

    public function connect()
    {
        return $this->sql->connect();
    }

    public function createTable()
    {

        $tb = 'CREATE TABLE IF NOT EXISTS ' . TAB_NAME .'(';
        $tb .= ' id int(6)  AUTO_INCREMENT PRIMARY KEY,';
        $tb .= ' links TEXT,';
        for ($i = 1; $i <= TAB_FIELDS; $i++)
            $tb .= ' field' . $i . ' TEXT,';
        $tb = substr($tb, 0, -1) . ')';
        $tb .= 'ENGINE=InnoDB DEFAULT CHARSET=utf8';

        $commands = [ $tb,
            'CREATE TABLE IF NOT EXISTS proxy (
            id int(6) AUTO_INCREMENT PRIMARY KEY,
            IP  VARCHAR (20) NOT NULL,
            port  VARCHAR (6),
            type  VARCHAR (6),
            anonim  INTEGER (2),
            delayResp  INTEGER (6),
            checkMy   TIMESTAMP,
            checkThey   DATETIME,
            failure   INTEGER (2),
            country  VARCHAR (80)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
        ];

        foreach ($commands as $command) {
            $this->connect()->exec($command);
        }
    }

}