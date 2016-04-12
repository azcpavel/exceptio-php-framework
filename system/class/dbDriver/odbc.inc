<?php
if (!file_exists($db)) {
				    die("Could not find database file in $db");
				}

$dsn = "odbc:DRIVER={Microsoft Access Driver (*.mdb, *.accdb)}; DBQ=$db; Uid=$user; Pwd=$pass;";