<?php

require_once('../resources/index.php');

use Config\Config;
use Config\IoC;

Config::set('levels', 3); // number of Spider pass levels
Config::set('outputWithUrl',1); // 1 - turn on add a column with url to output
Config::set('saveHTMLPage', 1); // 1 - save in storage html page
Config::set('writeLogs', 1); // 1 - true for spider and spiderGroup,  -1 - true for parserPage
Config::set('writeBenefitInFile', 1); // 1 - record benefits in file
Config::set('connectDB', 1); // 1 - turn on writing in DB
Config::set('proxyOn', 0); // 1 - turn on proxy
Config::set('curlHTTPInfo', 1); // 1 - turn on Curl HTTP_InFo
Config::set('respTimeout', 5); // number of seconds timeout
Config::set('connentTimeout', 4); //number of seconds connect timeout


//$clientHTTP = Ioc::resolve('http', 'webDriver');
$clientHTTP = Ioc::resolve('http', 'curl');
//$filterLinks = Ioc::resolve('filterlinks', 'paginator');
$filterLinks = Ioc::resolve('filterlinks', 'main');
//$filterLinks = Ioc::resolve('filterlinks', 'url_tail_links');
//$prepOutput = Ioc::resolve('output', 'hidemy');
$prepOutput = Ioc::resolve('output', 'turn');
//$prepOutput = Ioc::resolve('output', 'straight');
$DB = new \DB\DBPdoExecute(Ioc::resolve('store', 'mysql'));
