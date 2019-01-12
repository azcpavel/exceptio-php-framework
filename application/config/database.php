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
$db_config['default']['charset']	= 'utf8mb4';
$db_config['default']['collation'] 	= 'utf8mb4_unicode_ci';
$db_config['default']['engine']		= null;

//Second database 

// $db_config['db2']['driver'] 	= 'mysql'; //Both 'mysql' and 'mysqli' use "mysql".
// $db_config['db2']['host'] 		= 'localhost';
// $db_config['db2']['user'] 		= '';
// $db_config['db2']['pass'] 		= '';
// $db_config['db2']['db']   		= '2';
// $db_config['db2']['dbPrefix']   = '';
// $db_config['db2']['port']  		= '3306';
// $db_config['db2']['service']  	= '';
// $db_config['db2']['protocol']  	= '';
// $db_config['db2']['server']  	= '';
// $db_config['db2']['uid']  		= '';
// $db_config['db2']['options']  	= array();
// $db_config['db2']['autocommit'] = true;
// $db_config['db2']['preExecute'] = array(); //Any commands that will execute after db connect
// $db_config['db2']['useDbEscape'] = false; //will add db escape to db query for mysql (`), for sqlServ ([])
// $db_config['db2']['charset']	= 'utf8mb4';
// $db_config['db2']['collation'] 	= 'utf8mb4_unicode_ci';
// $db_config['db2']['engine']		= null;

//So on
?>