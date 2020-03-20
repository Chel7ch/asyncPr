<?php
require_once('../vendor/autoload.php');

define('CONNECT_DB', '1'); // 1 - turn on writing in DB
define('LEVELS', 4); // number of Spider pass levels

//$clientHTTP = new Client\HttpPHPWebDriver($url, 'chrome');
$clientHTTP = new Client\HttpCurl($url, $header);

//$filterLinks = new FilterLinks\ PaginatorCleanLinks();
$filterLinks = new FilterLinks\ MainCleanLinks();
$prepOutput = new Prepeare\ PrHidemyName();
//$prepOutput = new Prepeare\ TurnOverOutput();
$DB = new \DB\DBPdoCRUD(new\DB\MYSQLConnection);
