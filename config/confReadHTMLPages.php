<?php

require_once('../resources/index.php');

use Config\Config;
use Config\IoC;


Config::set('connectDB', 0); // 1 - turn on writing in DB
Config::set('outputWithUrl',0); // 1 - turn on add a column with url to output


//$filterLinks = Ioc::resolve('filterlinks', 'empty');
//$filterLinks = Ioc::resolve('filterlinks', 'paginator');
$filterLinks = Ioc::resolve('filterlinks', 'main');
//$filterLinks = Ioc::resolve('filterlinks', 'url_tail_links');
$prepOutput = Ioc::resolve('output', 'turn');
$DB = new \DB\DBPdoExecute(Ioc::resolve('store', 'mysql'));

