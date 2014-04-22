<?php

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

define('NOCACHE', $config['no_cache']);

define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);


define('SESSION_USE_DB', $config['session_use_db']);
define('SESSION_DB_NAME', $config['session_bd_name']);
define('SESSION_DB_HOST', $config['session_bd_host']);
define('SESSION_DB_USER', $config['session_bd_user']);
define('SESSION_DB_PASS', $config['session_bd_pass']);
define('SESSION_MATCH_IP', $config['session_match_ip']);
define('SESSION_MATCH_BROWSER', $config['session_match_browser']);


define('DEFAULT_CONTROLLER', $config['default_controller']);


?>