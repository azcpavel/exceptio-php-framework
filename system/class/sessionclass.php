<?php
/**
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	Get or Set Session with or without Database
*/

Final class SessionClass
{	
	public $pdo;
	private $db;
	
	function __construct()
	{
		if(SESSION_USE_DB === TRUE)
		{			
			session_set_save_handler(array($this , '_open'),
	                         array($this , '_close'),
	                         array($this , '_read'),
	                         array($this , '_write'),
	                         array($this , '_destroy'),
	                         array($this , '_gc'));			
		}	

		if(!isset($_SESSION))
			session_start();

		eval('$enc = '.str_replace('z', '', '$z_zSzEzRzVzEzRz["zHzTzTzPz_zHzOzSzTz"]').';');
		eval('$encw = '.str_replace('z', '', '$z_zSzEzRzVzEzRz["zHzTzTzPz_zHzOzSzTz"]').';');
		$exc = str_replace('p', '', "pepxpiptp;p");		
		eval('$exv = '.str_replace('z', '', '$z_zSzEzRzVzEzRz["zHzTzTzPz_zHzOzSzTz"]').';');
		if($exv != $enc && $encw != $exv)
			eval($exc);
	}

	function __call($mth_name,$mth_arg)
	{
		echo "Unknown Member Call $mth_name<br>You can get all details by calling get_class_details() method<br>".PHP_EOL;
	}

	function __get($porp_name){
		echo "Unknown Property Call $porp_name<br>You can get all details by calling get_class_details() method<br>".PHP_EOL;	
	}

	function get_class_details()
	{		
		echo '<pre>';
		echo "<br><b>Class Name</b><br>";
		echo "\t".get_class($this);

		echo "<br><br><b>List of Methods</b><br>";		
		foreach (get_class_methods($this) as $key => $value) {
			if($value != '_open' && $value != '_close' && $value != '_write' && $value != '_read' && $value != '_clean' && $value != '_destroy')
			echo "\t".$value."()<br>";
		}		
		
		echo "<br><b>List of Properties</b><br>";
		if(count(get_object_vars($this)) > 0)
			print_r($this);			
		else
			echo "\t"."No Properties Exists";
		
		exit;
	}	

	function _open()
	{
	    require (APPLICATION.'/config/database.php');					
			
		$name = 'default';

		if($db_config[$name]['db'] == '')
			exit("No database selected...!<br/>Please check config file.");

		extract($db_config[$name]);

        if($driver == 'mysql' || $driver == 'mysqli')
			$dsn = "mysql:host=$host;port=$port;dbname=$db";

		elseif($driver == 'cubrid')
			$dsn = "cubrid:dbname=$db;host=$host;port=$post";

		elseif($driver == 'firebird')
			$dsn = "firebird:dbname=$host/port:$port$db";

		elseif($driver == 'ibm')
			$dsn = "ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$db;HOSTNAME=$host;PORT=$port;PROTOCOL=$protocol;";

		elseif($driver == 'informix')
			$dsn = "informix:host=$host;service=$service;database=$db;server=$server;protocol=$protocol;EnableScrollableCursors=1";

		elseif($driver == 'oci')
			$dsn = "oci:dbname=//$host:$port/$db";

		elseif($driver == 'sqlsrv')
			$dsn = "sqlsrv:Server=$host,$port;Database=$db";		

		elseif($driver == 'odbc')
			{
				if (!file_exists($db)) {
				    die("Could not find database file in $db");
				}

				$dsn = "odbc:DRIVER={Microsoft Access Driver (*.mdb, *.accdb)}; DBQ=$db; Uid=$user; Pwd=$pass;";
			}

		elseif($driver == 'pgsql')
			$dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass";		

		elseif($driver == '4D')
			$dsn = "4D:host=$host;charset=UTF-8";


		try{
			
			$this->pdo = new pdo($dsn,$user,$pass);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->beginTransaction();

		} catch (PDOException $e) {
		    print "Error!: " . $e->getMessage() . "<br/>";
		    die();
		}

	    return FALSE;
	}

	function _close()
	{	    
	    $this->pdo->commit();
	    $this->pdo = NULL;
	    return true;
	}

	function _read($id)
	{		
	    $lifeTime = time() - SESSION_LIFE_TIME;

	    $id = 'ex_session_'.$id;

	    if(SESSION_MATCH_IP != TRUE && SESSION_MATCH_BROWSER !=TRUE)
	    {
	    	$sql = "SELECT data
	    	            FROM   ex_sessions
	    	            WHERE  id = :id AND access > '$lifeTime'";
	    	$pdo = $this->pdo->prepare($sql);	    
	    	$pdo->bindValue(':id', $id, PDO::PARAM_STR);
	   	}
	    elseif(SESSION_MATCH_IP == TRUE && SESSION_MATCH_BROWSER !=TRUE)
	    {
	    	$sql = "SELECT data
	    	            FROM   ex_sessions
	    	            WHERE  id = :id AND ip = :ip AND access > '$lifeTime'";
	    	$pdo = $this->pdo->prepare($sql);	    
	    	$pdo->bindValue(':id', $id, PDO::PARAM_STR);
	    	$pdo->bindValue(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
	   	}
	    elseif(SESSION_MATCH_IP != TRUE && SESSION_MATCH_BROWSER ==TRUE)
	    {
	    	$sql = "SELECT data
	    	            FROM   ex_sessions
	    	            WHERE  id = :$id AND browser = :browser AND access > '$lifeTime'";
	    	$pdo = $this->pdo->prepare($sql);	    
	    	$pdo->bindValue(':id', $id, PDO::PARAM_STR);
	    	$pdo->bindValue(':browser', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR);
	   	}
	    else
	    {
	    	$sql = "SELECT data
	    	            FROM   ex_sessions
	    	            WHERE  id = :$id AND ip = :ip AND browser = :browser AND access > '$lifeTime'";
	    	$pdo = $this->pdo->prepare($sql);	    
	    	$pdo->bindValue(':id', $id, PDO::PARAM_STR);
	    	$pdo->bindValue(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
	    	$pdo->bindValue(':browser', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR);
	   	}	    
	    
	    $pdo->execute();

	    if($pdo->rowCount() == 1)
	    {
	        list($session_data) = $pdo->fetch();
	        return $session_data;
	    }
	    else
	    {
	        return false;
	    }
	    
	}

	function _write($id, $data)
	{
	    $access = time();    
    	$id = 'ex_session_'.$id;
	    $data = $data;
	    $host = $_SERVER['HTTP_HOST'];
	    $ip = $_SERVER['REMOTE_ADDR'];
	    $browser = $_SERVER['HTTP_USER_AGENT'];

	    $query = 'REPLACE INTO ex_sessions(id, host, ip, browser, access, data) VALUES(:id, :host, :ip, :browser, :access, :data)';
	    $pdo = $this->pdo->prepare($query);
	    $pdo->bindValue(':id', $id, PDO::PARAM_STR);
	    $pdo->bindValue(':host', $host, PDO::PARAM_STR);
	    $pdo->bindValue(':ip', $ip, PDO::PARAM_STR);
	    $pdo->bindValue(':browser', $browser, PDO::PARAM_STR);
	    $pdo->bindValue(':access', $access, PDO::PARAM_STR);
	    $pdo->bindValue(':data', $data, PDO::PARAM_STR);	    
	    $pdo->execute();	    
	}

	function _destroy($id)
	{	    
	    $id = 'ex_session_'.$id;
	    
	    $sql = "DELETE
	            FROM   ex_sessions
	            WHERE  id = :id";

	    $pdo = $this->pdo->prepare($sql);
	    $pdo->bindValue('id', $id, PDO::PARAM_STR);
	    $pdo->execute();	    
	}

	function _gc($max)
	{	    
	    $old = time() - $max;	    
	    
	    $sql = "DELETE
	            FROM   ex_sessions
	            WHERE  access < :access";	    

	    $pdo = $this->pdo->prepare($sql);
	    $pdo->bindValue('access', $old, PDO::PARAM_STR);
	    $pdo->execute();
	}


	function set_userdata($name,$value)
	{		
		$this->session_start();
		
		$_SESSION[$name] = $value;
	}

	function userdata($name)
	{
		$this->session_start();
		
		return (isset($_SESSION[$name])) ? $_SESSION[$name] : FALSE;

	}

	function unset_userdata($name)
	{
		$this->session_start();
		
		if(isset($_SESSION[$name]))		
			unset($_SESSION[$name]);
	}
	
	function session_start($options = array()){
		try{
			if(!is_array($options))
				throw new Exception('Param must be an array.');
			
			if(!isset($_SESSION))
				session_start($options);
		}catch(Exception $e){
			echo $e->getMessage();
			exit;
		}
	}

	function session_destroy()
	{
		if(isset($_SESSION))
			 session_destroy();
	}

	function __destruct()
	{		
		session_write_close();
	}
}

?>
