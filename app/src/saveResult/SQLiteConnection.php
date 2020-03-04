<?php
namespace DB;

use PDO;

class SQLiteConnection implements IDBConnection
{
    private $pdo;

    public function __construct()
    {
        file_exists(PROJECT_DIR . '/db/') ?: mkdir(PROJECT_DIR . '/db/');
    }

    public function connect()
    {
        if ($this->pdo == null) {
            $this->pdo = new PDO("sqlite:" . DBConfig::PATH_TO_SQLITE_FILE);
            if (!file_exists(DBConfig::PATH_TO_SQLITE_FILE))
                throw new \Exception('There are not database!');
        }

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        return $this->pdo;
    }
}