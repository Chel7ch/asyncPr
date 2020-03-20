<?php

require_once('../vendor/autoload.php');

define('CONNECT_DB', '1'); // 1 - turn on writing in DB
define('COUNT_GOOD_PROXY', 50 ); // количество  прокси при котором закончится проверка
define('REQUEST_AT_TIME', 50 ); // request a proxy at a time
define('MAX_CHECKS', 2500); // maximum number of checks


//$clientHTTP = new Client\HttpPHPWebDriver($url, 'chrome');
$clientHTTP = new Client\HttpCurl($url, $header);
$DB = new \DB\DBPdoCRUD(new\DB\MYSQLConnection);