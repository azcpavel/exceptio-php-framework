<?php
$db_config['default']['driver'] 	= 'mysql'; //Both 'mysql' and 'mysqli' use "mysql".
$db_config['default']['host'] 		= 'localhost';
$db_config['default']['user'] 		= '';
$db_config['default']['pass'] 		= '';
$db_config['default']['db']   		= '';
$db_config['default']['dbPrefix']   = '';
$db_config['default']['port']  		= '3306';
$db_config['default']['service']  	= '';
$db_config['default']['protocol']  	= '';
$db_config['default']['server']  	= '';
$db_config['default']['uid']  		= '';
$db_config['default']['options']  	= array();
$db_config['default']['autocommit'] = true;
$db_config['default']['preExecute'] = array(); //Any commands that will execute after db connect
$db_config['default']['useDbEscape'] = false; //will add db escape to db query for mysql (`), for sqlServ ([])
?>