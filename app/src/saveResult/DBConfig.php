<?php

namespace DB;

class DBConfig
{
    const PATH_TO_SQLITE_FILE = PROJECT_DIR . '/db/' . PROJECT . '.db';

    const HOST = 'localhost';
    const DBASE = DB_NAME;
    const USER = 'root';
    const PASS = '';
    const CHARSET = 'utf8';
}