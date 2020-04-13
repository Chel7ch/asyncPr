<?php

require_once('../resources/index.php');

use Config\Config;
use Config\IoC;

Config::set('proxyOn', 1); // 1 - turn on proxy
Config::set('countGoodProxy', -1); // the number of proxies at which the check will end; -1 without limits
Config::set('multiRequest', 10); // number of parallel requests
Config::set('curlHTTPInfo', 1); // 1 - turn on Curl HTTP_InFo
Config::set('respTimeout', 5); // number of seconds timeout
Config::set('connentTimeout', 4); //number of seconds connect timeout
Config::set('connectDB', 1); // 1 - turn on writing in DB


$clientHTTP = Ioc::resolve('http', 'curl');
$DB = new \DB\DBPdoExecute(Ioc::resolve('store', 'mysql'));
