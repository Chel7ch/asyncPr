<?php

namespace DB;


use Config\Config;

class DBCreateTable
{


    public function __construct(IDBConnection $sql)
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
        for ($i = 1; $i <= Config::get('tabFields'); $i++)
            $tb .= ' field' . $i . ' TEXT,';
        $tb = substr($tb, 0, -1) . ')';
        $tb .= 'ENGINE=InnoDB DEFAULT CHARSET=utf8';
        /**
         * id
         * field1 - IP:Port
         * field2 - type
         * field3 - anonim
         * field4 - country
         * field5 - failure
         * field6 - DATETIME
         */
        $commands = [ $tb,
            'CREATE TABLE IF NOT EXISTS collect_proxy (
            id int(6) AUTO_INCREMENT PRIMARY KEY,
            field1  VARCHAR (22) NOT NULL,
            field2  VARCHAR (25),
            field3  VARCHAR (10),
            field4  VARCHAR (50),
            field5  INTEGER (2),
            date    DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;',
            /**
             * id
             * field1 - IP:Port
             * field2 - type
             * field3 - anonim
             * field4 - delay
             */
            'CREATE TABLE IF NOT EXISTS check_proxy (
            id int(6) AUTO_INCREMENT PRIMARY KEY,
            field1  VARCHAR (22) NOT NULL,
            field2  VARCHAR (15),
            field3  VARCHAR (10),
            field4  VARCHAR (6),
            date    DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
        ];

        foreach ($commands as $command) {
            $this->connect()->exec($command);
        }
    }

}