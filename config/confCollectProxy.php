<?php

require_once('../resources/index.php');

use Config\Config;
use Config\IoC;

$scratch = array(
    '//tbody/tr/td[1]',
    '//tbody/tr/td[2]',
    '//tbody/tr/td[5]',
    '//tbody/tr/td[6]',
    '//tbody/tr/td[3]//span[@class="country"]',
);

Config::set('levels', 50); // number of Spider pass levels
Config::set('connectDB', 1); // 1 - turn on writing in DB
Config::set('proxyOn', 0); // 1 - turn on proxy
Config::set('tail', '/proxy-list/'); // 1 - turn on proxy
Config::set('url', 'https://hidemy.name');
Config::set('tabFields', count($scratch));


$clientHTTP = Ioc::resolve('http', 'proxyWebDriver');
$filterLinks = Ioc::resolve('filterlinks', 'paginator');
$prepOutput = Ioc::resolve('output', 'hidemy');
$DB = new \DB\DBPdoExecute(Ioc::resolve('store', 'mysql'));

