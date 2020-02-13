<?php
namespace DB;

use PDO;
use DB\MYSQLConnection;

class SQLiteCreateTable
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function insertDB($query){
      $q =  $this->pdo->exec($query);
      return  $q;
    }

    public function selectDB(){
//        $stat = $this->pdo->query('SELECT COUNT(project_id) FROM benefits ')->fetchColumn();
        $st = $this->pdo->query('SELECT * FROM benefits');
        $results = $st->fetchAll();
        foreach ($results as $row){
            echo $row['id'].' ';
            echo $row['links'].' ';
            echo $row['field1'].'<br> ';
//            echo $row['field2'].' ';
//            echo $row['field3'].'<br> ';
        }
    }

    public function createTables()
    {
//        $tb = 'CREATE TABLE IF NOT EXISTS benefits (';
//        $tb = $tb . ' id  INTEGER PRIMARY KEY,';
//        $tb = $tb . ' links TEXT,';
//        for ($i = 1; $i <= TAB_FIELDS; $i++) $tb = $tb . ' field' . $i . ' TEXT,';
//        $tb = substr($tb, 0, -1) . ')';

        $commands = [ //$tb,
            'CREATE TABLE IF NOT EXISTS tasks (
            task_id INTEGER PRIMARY KEY,
            task_name  VARCHAR (255) NOT NULL,
            completed  INTEGER NOT NULL,
            start_date TEXT,
            completed_date TEXT,
            project_id VARCHAR (255),
            FOREIGN KEY (project_id)
            REFERENCES projects(project_id) ON UPDATE CASCADE
                                         ON DELETE CASCADE)',
            'CREATE TABLE IF NOT EXISTS documents (
            document_id INTEGER PRIMARY KEY,
            mime_type   TEXT    NOT NULL,
            doc  BLOB)'
        ];
        $commands ='CREATE TABLE IF NOT EXISTS tasks (
            task_id INTEGER PRIMARY KEY,
            task_name  VARCHAR (255) NOT NULL,
            completed  INTEGER NOT NULL,
            start_date TEXT,
            completed_date TEXT,
            project_id VARCHAR (255)
            )';
//        foreach ($commands as $command) {
            $this->pdo->exec($commands);
//        }
    }

    public function getTableList()
    {
        $stmt = $this->pdo->query("SELECT name
                               FROM sqlite_master
                               WHERE type = 'table'
                               ORDER BY name");
        $tables = [];
        while ($row = $stmt->fetch()) {
            $tables[] = $row['name'];
        }

        return $tables;
    }
}