<?php
/*
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
	private $db;
	
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
	    	
	    require (APPLICATION.'/config/database.php');
		
		$db_get_all_config = $db_config;		
		
		$name = 'default';

		if($db_get_all_config[$name]['db'] == '')
			exit("No database selected...!<br/>Please check config file.");

		// echo "<pre>";print_r($this->db);exit;

		$this->db = new dbClass($db_get_all_config[$name]['driver'],$db_get_all_config[$name]['host'],$db_get_all_config[$name]['user'],
			$db_get_all_config[$name]['pass'],$db_get_all_config[$name]['db'],$db_get_all_config[$name]['dbPrefix'],$db_get_all_config[$name]['port'],
			$db_get_all_config[$name]['service'],$db_get_all_config[$name]['protocol'],$db_get_all_config[$name]['server'],
			$db_get_all_config[$name]['uid'],$db_get_all_config[$name]['options']);			

	    return FALSE;
	}

	function _close()
	{	    
	    
	    return $this->db = NULL;
	}

	function _read($id)
	{
		echo " read ";
	    $id = 'ex_session_'.$id;

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

	    if ($result = $this->db->query($sql))
	    {	        
	        if ($result->num_rows() > 0)
	        {
	            $record = $result->row_array();

	            return $record['data'];
	        }
	    }

	    return '';
	}

	function _write($id, $data)
	{	    
	    $access = time();
	    echo " write ";
	    // $data = array(
	    // 	'id' => 'ex_session_'.$id,
		   //  'access' => $access,
		   //  'data' => $data,
		   //  'ip' => $_SERVER['REMOTE_ADDR'],
		   //  'browser' => $_SERVER['HTTP_USER_AGENT']
		   //  );
		$data = array(
	    	'id' => 'sdgfdfsg',
		    'access' => 'sdgfdfsg',
		    'data' => 'sdgfdfsg',
		    'ip' => 'sdgfdfsg',
		    'browser' => 'sdgfdfsg'
		    );	    	
	    // $sql = "REPLACE INTO ex_sessions VALUES ('$id', '$ip', '$browser', '$access', '$data')";
	    
	    $this->db->insert('ex_sessions', $data, 'REPLACE');	    
	}

	function _destroy($id)
	{	    
	    $id = 'ex_session_'.$id;
	    
	    $sql = "DELETE
	            FROM   ex_sessions
	            WHERE  id = '$id'";

	    $this->db->delete('ex_sessions',array('id'=>$id));
	}

	function _gc($max)
	{
	    
	    $old = time() - $max;	    
	    
	    $sql = "DELETE
	            FROM   ex_sessions
	            WHERE  access < '$old'";	    
	    $this->db->delete('ex_sessions',array('access <'=>$old));
	}


	function set_userdata($name,$value)
	{		
		echo " set ";
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
