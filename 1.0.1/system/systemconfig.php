<?php
/**
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	System Constants
*/

define('APPLICATION','application');

require_once(APPLICATION.'/config/config.php');
require_once(APPLICATION.'/config/route.php');

if($config['base_url'] === ''){

	$config['base_url']  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';

	$config['base_url'] .= $_SERVER['HTTP_HOST'];

	$config['base_url'] .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
}



$config['base_host']  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';

$config['base_host'] .= $_SERVER['HTTP_HOST'];


$config['base_dir'] = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);


define('BASEPATH', $config['base_url']);

define('BASEHOST', $config['base_host']);

define('BASEDIR', $config['base_dir']);

define('INDEXPHP', $config['hide_index']);

define('NOCACHE', $config['no_cache']);

define('ENCRYPT_SALT', $config['encryption_key']);

define('COOKIE_ENCRYPT', $config['cookie_encryption']);
define('COOKIE_LIFETIME', $config['cookie_lifetime']);

define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);


define('SESSION_USE_DB', $config['session_use_db']);
define('SESSION_MATCH_IP', $config['session_match_ip']);
define('SESSION_MATCH_BROWSER', $config['session_match_browser']);
define('SESSION_LIFE_TIME', $config['session_life_time']);


define('DEFAULT_CONTROLLER', $config['default_controller']);

?>
