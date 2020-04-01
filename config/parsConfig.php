<?php
require_once('../vendor/autoload.php');

define('CONNECT_DB', '0'); // 1 - turn on writing in DB
define('SAVE_HTML_PAGE', 1);// 1 - save in storage html page
define('MULTI_REQUEST', 5); // number of concurrent requests
define('LEVELS', 3); // number of Spider pass levels
define('PROXY_ON', 1); // 1 - turn on proxy
define('CURL_HTTP_INFO', '1'); // 1 - turn on Curl HTTP_InFo
define('CURL_TIMEOUT', '5');// number of seconds timeout
define('CURL_CONNECTTIMEOUT', '4');//number of seconds connect timeout


//$clientHTTP = new Client\HttpPHPWebDriver($url, 'chrome');
$clientHTTP = new Client\HttpCurl($url, $header);
$filterLinks = new FilterLinks\ PaginatorCleanLinks();
//$filterLinks = new FilterLinks\ MainCleanLinks();
//$prepOutput = new Prepare\ PrHidemyName();
$prepOutput = new Prepare\ TurnOverOutput();
$DB = new \DB\DBPdoExecute(new\DB\MYSQLConnection);