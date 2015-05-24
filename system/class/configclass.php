<?php
/**
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	Config Class
*/

final class ConfigClass{

	private $config;	

	function __construct()
	{
		
	}

	function __call($mth_name,$mth_arg)
	{
		echo "Unknown Member Call $mth_name<br>You can get all details by calling get_class_details() method";
	}

	function load($configFile){
		if(file_exists(APPLICATION.'/config/'.$configFile.'.php')){
			require (APPLICATION.'/config/'.$configFile.'.php');
			$this->config = $config;
		}
		else
			exit("Config not found in ".APPLICATION.'/config/'.$configFile.'.php');
	}

	function item($name)
	{
		return $this->config[$name];
	}


	function get_class_details()
	{		
		echo '<pre>';
		echo "<br><b>Class Name</b><br>";
		echo "\t".get_class($this);

		echo "<br><br><b>List of Methods</b><br>";
		foreach (get_class_methods($this) as $key => $value) {
			echo "\t".$value."()<br>";
		}		
		
		echo "<br><b>List of Properties</b><br>";
		if(count(get_object_vars($this)) > 0)
			print_r($this);
		else
			echo "\t"."No Properties Exists";
		
		exit;
	}
	
}


?>
