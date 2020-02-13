<?php
# file ini.php should be in the folder app
(substr($url, -1) == '/')? $pr = substr($url, 0, -1) : $pr = $url;
$t = array('https://' => '', 'http://' => '');
$project = strtr($pr, $t);

$t = array('.' => '_');
$tabName = strtr($project, $t);

/** Folders */
define('PROJECT', $project);
define('STRLEN_PROJECT', strlen($project));
define('TAB_FIELDS', count($scratch)); // count column in table DB
define('DIR_SCRIPT', $_SERVER['DOCUMENT_ROOT'] . '/pars');
define('PROJECT_DIR', DIR_SCRIPT.'/storage/projects/'.PROJECT);
define('COOKIE_FILE', DIR_SCRIPT. '/storage/cookies');
define('LOG_FILE', DIR_SCRIPT. '/storage/logs/php_errors.log');
define('ERR_RESP_FILE', PROJECT_DIR . '/logs/err_response.csv');
/** Setting */
define('USLEEP', 0.2 *100000); // milliseconds waiting for script
define('REPEAT_ERR_URL', 0); // number repeat of the repeatErrorURL
define('REPEAT_ERR_URL_DELAY', 60); // time between repeatErrorURL
define('LEVELS', 3); // number of Spider pass levels
define('SAVE_HTML_PAGE', 0);// 1 - save
//define('PROFILE', '- user-data-dir');// профиль в браузере
/** DB */
define('TURN_OVER_BENEFIT', 1); // 1 - true turnOverOutput 2 - true straightOutput
define('DB_NAME', 'parser');
define('TAB_NAME', $tabName);


file_exists(DIR_SCRIPT. '/storage')? :mkdir(DIR_SCRIPT.'/storage');
file_exists(DIR_SCRIPT. '/storage/logs')? :mkdir(DIR_SCRIPT. '/storage/logs');
file_exists(DIR_SCRIPT.'/storage/projects')? :mkdir(DIR_SCRIPT.'/storage/projects');
file_exists(COOKIE_FILE)? :mkdir(COOKIE_FILE);
file_exists(PROJECT_DIR)? :mkdir(PROJECT_DIR);


ini_set("memory_limit", "1000M"); //default 128M
ini_set('max_execution_time',0);
/** Output errors */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
/** Saving errors */
ini_set('log_errors', 'On');
ini_set('error_log', LOG_FILE );


//if(!file_exists(PROJECT_DIR.'\screenshots')) mkdir(PROJECT_DIR.'\screenshots');
//if(!file_exists(PROJECT_DIR.'\benefit')) mkdir(PROJECT_DIR.'\benefit');
//if(!file_exists(PROJECT_DIR.'\images')) mkdir(PROJECT_DIR.'\images');
//define('DIR_IMAGES', PROJECT_DIR .'\images\\');


