<?php
/**
* 
*/
Final class SessionClass
{
	private $sess_db;
	
	function __construct()
	{
		if(SESSION_USE_DB === TRUE)
		session_set_save_handler(array($this , '_open'),
                         array($this , '_close'),
                         array($this , '_read'),
                         array($this , '_write'),
                         array($this , '_destroy'),
                         array($this , '_gc'));

		if(!isset($_SESSION))
			session_start();
	}

	function __call($mth_name,$mth_arg)
	{
		echo "Unknown Member Call $mth_name<br>You can get all details by calling get_class_details() method";
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

	    $db_user = SESSION_DB_USER;
	    $db_pass = SESSION_DB_PASS;
	    $db_host = SESSION_DB_HOST;
	    $db_name = SESSION_DB_NAME;
	    
	    if ($this->sess_db = new mysqli($db_host,$db_user,$db_pass))
	    {
	        return $this->sess_db->select_db($db_name);
	    }
	    else
	    	 die("Error " . $this->sess_db->error());
	    
	    return FALSE;
	}

	function _close()
	{	    
	    
	    return $this->sess_db->close();
	}

	function _read($id)
	{
	   

	    $id = 'ex_session_'.$this->sess_db->real_escape_string($id);

	    if(SESSION_MATCH_IP != TRUE && SESSION_MATCH_BROWSER !=TRUE)
	    $sql = "SELECT data
	            FROM   ex_sessions
	            WHERE  id = '$id'";

	    elseif(SESSION_MATCH_IP == TRUE && SESSION_MATCH_BROWSER !=TRUE)
	    $sql = "SELECT data
	            FROM   ex_sessions
	            WHERE  id = '$id' AND ip = '$_SERVER[REMOTE_ADDR]'";

	    elseif(SESSION_MATCH_IP != TRUE && SESSION_MATCH_BROWSER ==TRUE)
	    $sql = "SELECT data
	            FROM   ex_sessions
	            WHERE  id = '$id' AND browser = '$_SERVER[HTTP_USER_AGENT]'";
	    else
	    $sql = "SELECT data
	            FROM   ex_sessions
	            WHERE  id = '$id' AND ip = '$_SERVER[REMOTE_ADDR]' AND browser = '$_SERVER[HTTP_USER_AGENT]'";

	    if ($result = $this->sess_db->query($sql))
	    {
	        if ($result->num_rows)
	        {
	            $record = $result->fetch_assoc();

	            return $record['data'];
	        }
	    }

	    return '';
	}

	function _write($id, $data)
	{   
	   

	    $access = time();
	    
	    $id = 'ex_session_'.$this->sess_db->real_escape_string($id);
	    $access = $this->sess_db->real_escape_string($access);
	    $data = $this->sess_db->real_escape_string($data);
	    $ip = $_SERVER['REMOTE_ADDR'];
	    $browser = $_SERVER['HTTP_USER_AGENT'];

	    $sql = "REPLACE 
	            INTO    ex_sessions
	            VALUES  ('$id', '$ip', '$browser', '$access', '$data')";

	    return $this->sess_db->query($sql);
	}

	function _destroy($id)
	{
	   
	    
	    $id = 'ex_session_'.$this->sess_db->real_escape_string($id);

	    $sql = "DELETE
	            FROM   ex_sessions
	            WHERE  id = '$id'";

	    return $this->sess_db->query($sql);
	}

	function _gc($max)
	{
	    
	    
	    $old = time() - $max;
	    $old = $this->sess_db->real_escape_string($old);

	    $sql = "DELETE
	            FROM   ex_sessions
	            WHERE  access < '$old'";

	    return $this->sess_db->query($sql);
	}


	function set_userdata($name,$value)
	{		
		$_SESSION[$name] = $value;		
	}

	function userdata($name)
	{
		return (isset($_SESSION[$name])) ? $_SESSION[$name] : FALSE;

	}

	function unset_userdata($name)
	{
		if(isset($_SESSION[$name]))		
			unset($_SESSION[$name]);
	}

	function session_destroy()
	{
		if(isset($_SESSION))
			 session_destroy();
	}
}

?>