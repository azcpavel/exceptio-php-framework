<?php
/**
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	Get or Set COOKIE with or without encryption
*/

Final class CookieClass
{	
	function __construct()
	{
				
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

	private function encryptArray($value)
	{

	}

	private function decryptArray($index)
	{
		
	}	

	function set_userdata($name, $value, $encrypt = false, $options = array())	
	{
		$options = array_replace(array('lifetime' => COOKIE_LIFETIME,'path' => BASEDIR, 'domain' => $_SERVER['HTTP_HOST'], 'secure' => COOKIE_SECURE, 'httponly' => COOKIE_HTTPONLY), 
								$options);		
		setcookie($name,((COOKIE_ENCRYPT || $encrypt) ? ex_encrypt($value) : $value), $options['lifetime'], $options['path'], $options['domain'], $options['secure'], $options['httponly']);
	}

	function userdata($name, $decrypt = false)
	{
		return (isset($_COOKIE[$name])) ? ((COOKIE_ENCRYPT || $decrypt) ? ex_decrypt($_COOKIE[$name]) : $_COOKIE[$name]) : FALSE;
	}

	function unset_userdata($name)
	{
		if(isset($_COOKIE[$name])){		
			unset($_COOKIE[$name]);
			setcookie($name, null, -1);
		}
	}

	function cookie_destroy()
	{
		if(isset($_COOKIE))
		{
			foreach ($_COOKIE as $key => $value) {
				unset($_COOKIE[$key]);
				setcookie($key, null, -1);
			}
		}			 
	}

	function __destruct()
	{		
		
	}
}

?>
