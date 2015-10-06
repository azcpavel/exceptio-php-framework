<?php
/**
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	REQUEST and FILES Variable Get & Filter
*/
Final class InputClass
{
	
	function __construct()
	{
		# code...
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

	function POST($name,$clr = FALSE)
	{
		if(isset($_POST[$name]))
		{
			if($clr == TRUE)
				@$_POST[$name] = replace_regx(@$_POST[$name]);

			return $_POST[$name];
		}
	}

	function GET($name,$clr = FALSE)
	{
		if(isset($_GET[$name]))
		{
			if($clr == TRUE)
				@$_GET[$name] = replace_regx(@$_GET[$name]);

			return $_GET[$name];
		}
	}

	function REQUEST($name,$clr = FALSE)
	{
		if(isset($_REQUEST[$name]))
		{
			if($clr == TRUE)
				@$_REQUEST[$name] = replace_regx(@$_REQUEST[$name]);
			
			return $_REQUEST[$name];
		}
	}

	function FILES($name)
	{
		if(isset($_FILES[$name]))
			return $_FILES[$name];
	}

	function xss_Clean($input)
	{
		return replace_regx($input);
	}
	
}
?>
