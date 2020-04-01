<?php

require_once('../vendor/autoload.php');

define('CONNECT_DB', '0'); // 1 - turn on writing in DB
define('OUTPUT_WITH_URL', '0'); // 1 - turn on add a column with url to output

//$filterLinks = new FilterLinks\ NoCleanLinks;
$filterLinks = new FilterLinks\URLTailCleanLinks;
//$filterLinks = new FilterLinks\ PaginatorCleanLinks;
//$filterLinks = new FilterLinks\ MainCleanLinks;
$prepOutput = new Prepare\ TurnOverOutput();
$DB = new \DB\DBPdoExecute(new\DB\MYSQLConnection);
