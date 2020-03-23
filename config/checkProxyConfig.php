<?php

require_once('../vendor/autoload.php');

define('CONNECT_DB', '1'); // 1 - turn on writing in DB
define('PROXY_ON', '1'); // 1 - turn on proxy
define('COUNT_GOOD_PROXY', -1 ); // the number of proxies at which the check will end; -1 without limits
define('CURL_HTTP_INFO', '1'); // 1 - turn on Curl HTTP_InFo
define('CURL_TIMEOUT', '5');// number of seconds timeout
define('CURL_CONNECTTIMEOUT', '4');//number of seconds connect timeout
define('MULTI_REQUEST', 5); // number of concurrent requests


//$clientHTTP = new Client\HttpPHPWebDriver($url, 'chrome');
$clientHTTP = new Client\HttpCurl($url, $header);
$DB = new \DB\DBPdoCRUD(new\DB\MYSQLConnection);