<?php

$config['base_url'] = ''; //Leave empty for generate auto

$config['no_cache'] = 1; //Add no cache user can't go back after logout value = 1 for active, 0 for inactive

$config['hide_index'] = 1; //If you need to hide index.php from url value = 1 for active, 0 for inactive

$config['encryption_key'] = 'FFD'; //encryption key

$config['time_zone'] = NULL; //default time zone, NULL for default

$config['cookie_encryption'] = false; //will encryption apply for cookie value true or false
$config['cookie_lifetime'] =  time() + 31536000; //COOKIE lifetime in second
$config['cookie_secure'] = false; //will apply for cookie, value true or false
$config['cookie_httponly'] = true; //will apply for cookie, value true or false

$config['show_db_error'] = true; //show db error for query, value true or false

/*******************************************************************************************

CREATE TABLE IF NOT EXISTS ex_sessions(
id varchar(60) DEFAULT '0' NOT NULL,
host varchar(60) DEFAULT 'localhost' NOT NULL,
ip varchar(45) DEFAULT '0' NOT NULL,
browser varchar(120) NOT NULL,
access  int(10) unsigned DEFAULT 0 NOT NULL,
data text NOT NULL,
PRIMARY KEY (id),
KEY `ex_access` (`access`)
)Engine=InnoDB default charset=UTF8;

********************************************************************************************/
$config['session_use_db'] = FALSE; //Use database For session value = TRUE for active, FALSE for inactive
$config['session_match_ip'] = FALSE; //will session need to match IP
$config['session_match_browser'] = FALSE; //will session need to match browser
$config['session_life_time'] = 60 * 60; //SESSION lifetime in second

?>
