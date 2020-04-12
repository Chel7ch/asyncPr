<?php

require_once('../resources/index.php');

use Config\Config;
use Config\IoC;

Config::set('connectDB', 1); // 1 - turn on writing in DB
Config::set('levels', 1); // number of Spider pass levels
Config::set('saveHTMLPage', 1); // 1 - save in storage html page
Config::set('outputWithUrl', 0); // 1 - turn on add a column with url to output
Config::set('writeLogs', 1); // 1 - true
Config::set('proxyOn', 0); // 1 - turn on proxy
Config::set('curlHTTPInfo', 1); // 1 - turn on Curl HTTP_InFo
Config::set('respTimeout', 5); // number of seconds timeout
Config::set('connentTimeout', 4); //number of seconds connect timeout

//define('CONNECT_DB', '1'); // 1 - turn on writing in DB
//define('OUTPUT_WITH_URL', '0'); // 1 - turn on add a column with url to output
//define('SAVE_HTML_PAGE', 1);// 1 - save in storage html page
//define('WRITE_LOGS', 1); // 1 - true
//define('MULTI_REQUEST', 5); // number of concurrent requests
//define('LEVELS', 1); // number of Spider pass levels
//define('PROXY_ON', 0); // 1 - turn on proxy
//define('CURL_HTTP_INFO', '1'); // 1 - turn on Curl HTTP_InFo
//define('CURL_TIMEOUT', '5');// number of seconds timeout
//define('CURL_CONNECTTIMEOUT', '4');//number of seconds connect timeout


$clientHTTP = Ioc::resolve('http', 'webDriver');
//$clientHTTP = Ioc::resolve('http', 'curl');
//$filterLinks = Ioc::resolve('filterlinks', 'paginator');
//$filterLinks = Ioc::resolve('filterlinks', 'main');
$filterLinks = Ioc::resolve('filterlinks', 'url_tail_links');
//$prepOutput = Ioc::resolve('output', 'hidemy');
$prepOutput = Ioc::resolve('output', 'turn');
$DB = new \DB\DBPdoExecute(Ioc::resolve('store', 'mysql'));;
