<?php

require_once('../resources/index.php');

use Config\Config;
use Config\IoC;

$url = 'https://hidemy.name';
$url = parse_url($url, 0) . '://' . parse_url($url, 1);

$tail = '/proxy-list/';

$scratch = array(
    '//tbody/tr/td[1]',
    '//tbody/tr/td[2]',
    '//tbody/tr/td[5]',
    '//tbody/tr/td[6]',
    '//tbody/tr/td[3]//span[@class="country"]',
);

Config::setConfig($url, $scratch, $header,$tail);
Config::set('levels', 100); // number of Spider pass levels
Config::set('connectDB', 1); // 1 - turn on writing in DB
Config::set('proxyOn', 0); // 1 - turn on proxy


$clientHTTP = Ioc::resolve('http', 'proxyWebDriver');
$filterLinks = Ioc::resolve('filterlinks', 'paginator');
$prepOutput = Ioc::resolve('output', 'hidemy');
$DB = new \DB\DBPdoExecute(Ioc::resolve('store', 'mysql'));

