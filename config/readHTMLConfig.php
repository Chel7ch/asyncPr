<?php

require_once('../vendor/autoload.php');

define('CONNECT_DB', '1'); // 1 - turn on writing in DB


$prepOutput = new Prepeare\ TurnOverOutput();
$DB = new \DB\DBPdoCRUD(new\DB\MYSQLConnection);
