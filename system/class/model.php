<?php
/**
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	Base Model
*/

abstract class Ex_Model
{

	private static $all_instance;
	public	$load;
	public 	$session;
	public 	$input;
	public 	$server;
	public 	$globals;	
	

	#use Select, MkArrayObj;

	protected function __construct()
	{	
		self::$all_instance =& $this;

		$this->load 	 = new loadDBClass;		
		$this->session 	 = new sessionClass;
		$this->cookie 	 = new cookieClass;		
		$this->input 	 = new inputClass;	
		$this->server 	 = new serverClass;
		$this->globals 	 = new globalsClass;	
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
			echo "\t".$value."()<br>";
		}		
		
		echo "<br><b>List of Properties</b><br>";
		if(count(get_object_vars($this)) > 0)
			print_r($this);
		else
			echo "\t"."No Properties Exists";
		
		exit;
	}	

	static function &get_all_instance()
	{
		return self::$all_instance;
	}
	

	
}
?>
