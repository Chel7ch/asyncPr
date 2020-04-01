<?php

require_once('../vendor/autoload.php');

$url = 'https://hidemy.name';
$tail = '/proxy-list/';
$scratch = array(
    '//tbody/tr/td[1]',
    '//tbody/tr/td[2]',
    '//tbody/tr/td[5]',
    '//tbody/tr/td[6]',
    '//tbody/tr/td[3]//span[@class="country"]',
);

define('CONNECT_DB', '1'); // 1 - turn on writing in DB
define('TAIL',$tail); // part or full path of url
define('LEVELS',100); // number of Spider pass levels
define('USING_XPATH', 1); // 1 - search using XPATH expressions 0 - search using DiDom expressions



echo '<pre>';
//Prepeare\PrepInsertQuery::$tabName = 'collect_proxy';
//
//$clientHTTP = new Client\ProxyPHPWebDriver($url, 'chrome');
//$filterLinks = new FilterLinks\ PaginatorCleanLinks();
//$prepOutput = new Prepeare\ PrHidemyName();
//$DB = new \DB\DBPdoExecute(new\DB\MYSQLConnection);
