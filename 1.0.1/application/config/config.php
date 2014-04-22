<?php

$config['base_url'] = ''; //Leave empty for generate auto


$config['no_cache'] = 1; //Add no cache user can't go back after logout value = 1 for active, 0 for inactive



/*******************************************************************************************

CREATE TABLE IF NOT EXISTS ex_sessions(
id varchar(60) DEFAULT '0' NOT NULL,
ip varchar(45) DEFAULT '0' NOT NULL,
browser varchar(120) NOT NULL,
access  int(10) unsigned DEFAULT 0 NOT NULL,
data text NOT NULL,
PRIMARY KEY (id),
KEY `ex_access` (`access`)
)Engine=InnoDB default charset=UTF8;

********************************************************************************************/
$config['session_use_db'] = FALSE; //Use database For session value = 1 for active, 0 for inactive
$config['session_bd_name'] = '';
$config['session_bd_host'] = '';
$config['session_bd_pass'] = '';
$config['session_bd_user'] = '';
$config['session_match_ip'] = TRUE;
$config['session_match_browser'] = TRUE;

?>