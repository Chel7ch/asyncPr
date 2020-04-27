<?php

$project = parse_url($url, 1);

@define('DB_NAME', 'parser');
@define('SCRIPT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/async');
@define('PROJECT_DIR', SCRIPT_DIR . '/storage/projects/' . $project);
@define('PROJECT', $project);
@define('TAB_NAME', str_replace('.', '_', $project));
@define('CHROME_PROFILE', 'C:\Users\Dm\AppData\Local\Google\Chrome\User Data\Profile 1');

return [
    'project' => PROJECT,
    /** URL */
    'url' => parse_url($url, 0) . '://' . parse_url($url, 1),
    /** Folders */
    'scriptDir' => SCRIPT_DIR,
    'logsDir' => SCRIPT_DIR . '/storage/logs',
    'cookieDir' => SCRIPT_DIR . '/storage/cookies',
    'projectDir' => PROJECT_DIR,
    'saveHTMLDir' => PROJECT_DIR . '/htmlPages',
    'logErrRespDir' => PROJECT_DIR . '/logs',
    /** Files */
    'logFile' => SCRIPT_DIR . '/storage/logs/php_errors.log',
    'cookieFile' => SCRIPT_DIR . '/storage/logs/cookie.txt',
    'errRespFile' => SCRIPT_DIR . '/storage/projects/' . $project . '/logs/err_response.csv',
    'zeroErrRespFile' => SCRIPT_DIR . '/storage/projects/' . $project . '/logs/zero_err_response.csv',
    'goodProxyFile' => SCRIPT_DIR . '/storage/logs/good_proxy.csv',
    /** DB */
    'connectDB' => '0', // 1 - turn on writing in DB
    'tabName' => TAB_NAME,
    'tabFields' => count($scratch), // count column in table "project tabName" in DB
    /** Setting */
    'usleep' => 0.2 * 1000000, // mikroseconds waiting for script
    'levels' => 1, // number of Spider pass levels
    'forceReadErrResponseUrl' => 3, // the number of retry to reading pages with a error response from the server
    'saveHTMLPage' => 1, // 1 - save in storage html page
    'HTTPInfo' => 1, // 1 - turn on HTTP info
    /** Clean links */
    'tail' => $tail,
    /** benefit */
    'usingXPATH' => 1, // 1 - search using XPATH expressions 0 - search using DiDom expressions
    'outputWithUrl' => 0, // 1 - turn on add a column with url to output
    'scratch' => $scratch,
    /** prepare  output */
    'prepBenefit' => 1, // 1 - true turnOverOutput 0 - true straightOutput
    'prepQueryForDB' => 0, // 1 - for write in DB , 0 for write in file
    'writeLogs' => 1, // 1 - true for spider and spiderGroup,  -1 - true for parserPage
    'writeBenefitInFile' => 0, // 1 - record benefits in file
    /** proxy*/
    'proxyOn' => 0, // 1 - turn on proxy
    'workProxy' => array(),
    'multiRequest' => 5, // number of parallel requests
    'countGoodProxy' => 1000,  // the number of proxies at which the check will end; -1 without limits
    'saveGoodProxyInDB' => 0,  // 1 - save good proxies in DB ; 0 - save in file
    /** HTTP*/
    'curlHTTPInfo' => 0, // 1 - turn on Curl HTTP_InFo, 2 - detailed Curl HTTP_InFo
    'respTimeout' => 5, // number of seconds timeout
    'connentTimeout' => 4, //number of seconds connect timeout
    'userAgent' => null,
    'referer' => 'http://diesel.elcat.kg/',
    'header' => $header,
    'postData' => null,
//    'browserType' => 'firefox', //type browser for webDriver
    'browserType' => 'chrome', //type browser for webDriver
//    'browserType' => 'microsoftEdge', //type browser for webDriver
    /** own service */
    'nextStep' => 0, //0 - default
];
