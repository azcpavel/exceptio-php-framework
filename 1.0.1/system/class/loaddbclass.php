<?php
/**
* 
*/

Final class LoadDBClass
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
			echo "\t".$value."()<br>";
		}		
		
		echo "<br><b>List of Properties</b><br>";
		if(count(get_object_vars($this)) > 0)
			print_r($this);			
		else
			echo "\t"."No Properties Exists";
		
		exit;
	}

	
	function database($name = 0)
	{
		require (APPLICATION.'/config/database.php');
		
		$db_get_all_config = $db_config;

		$model =& get_model_instance();
		
		if($name === 0)
			$name = 'default';

		if($db_get_all_config[$name]['db'] == '')
			exit("No database selected...!<br/>Please check config file.");

		$model->db = new dbClass($db_get_all_config[$name]['driver'],$db_get_all_config[$name]['host'],$db_get_all_config[$name]['user'],
			$db_get_all_config[$name]['pass'],$db_get_all_config[$name]['db'],$db_get_all_config[$name]['port'],
			$db_get_all_config[$name]['service'],$db_get_all_config[$name]['protocol'],$db_get_all_config[$name]['server'],
			$db_get_all_config[$name]['uid']);

		return $model->db;
	}
	
}
?>