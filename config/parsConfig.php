<?php
require_once('../vendor/autoload.php');

define('CONNECT_DB', '0'); // 1 - turn on writing in DB
define('MULTI_REQUEST', 5); // number of concurrent requests
define('LEVELS', 3); // number of Spider pass levels
define('PROXY_ON', 1); // 1 - turn on proxy
define('CURL_HTTP_INFO', '1'); // 1 - turn on Curl HTTP_InFo
define('CURL_TIMEOUT', '5');// number of seconds timeout
define('CURL_CONNECTTIMEOUT', '4');//number of seconds connect timeout


