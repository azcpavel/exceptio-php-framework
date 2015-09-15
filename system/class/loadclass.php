<?php
/**
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	Controllers Model, Library, Helper loader
*/

Final class LoadClass
{	
	protected $session;
	protected $input;
	protected $server;

	function __construct()
	{
		$this->session 	= new sessionClass;
		$this->input 	= new inputClass;
		$this->server 	= new serverClass;
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
	

	function model($load_model_name)
	{
		$Controller =& get_controller_instance();

		$base_name = basename($load_model_name);

		if(file_exists(APPLICATION.'/models/'.$load_model_name.'.php'))
			{
				require(APPLICATION.'/models/'.$load_model_name.'.php');
				if(class_exists($base_name))
					$Controller->$base_name = new $base_name;
				else
					exit("Class $base_name not found in your application/models/".$load_model_name.'.php');
			}
		else
			exit("Model not found in your application/models/".$load_model_name.'.php');
	}

	function library($load_libraries_name = '',$config = '')
	{
		$Controller =& get_controller_instance();

		$base_name = basename($load_libraries_name);

		if(file_exists(DOCUMENT_ROOT.BASEDIR.SYSTEM.'/libraries/'.$load_libraries_name.'.php'))
			{
				require(SYSTEM.'/libraries/'.$load_libraries_name.'.php');
				if(class_exists($base_name))
					{
						if($base_name === 'imgresize' || $base_name === 'zend' || $base_name == 'exqrcode' || $base_name == 'emoticons')
						{
							$Controller->$base_name = new $base_name($config);
						}
						else
						{
							$Controller->$base_name = new $base_name($config);

							if(count($config) > 0)
							foreach ($config as $key => $value) {
								
								if(property_exists($Controller->$base_name, $key))
									$Controller->$base_name->$key = $value;
							}

							unset($key);
							unset($value);
						}

					}
				else
					exit("Class $base_name not found in ".SYSTEM.'/libraries/'.$load_libraries_name.'.php');
			}
		else
			exit("Libraries not found in ".SYSTEM.'/libraries/'.$load_libraries_name.'.php');
	}
	

	function helper($load_helper_page = 'main')
	{		

		if(file_exists(APPLICATION.'/helpers/'.$load_helper_page.'_helper.php'))
			require_once (APPLICATION.'/helpers/'.$load_helper_page.'_helper.php');
		else
			exit("Helper not found in your application/helpers/".$load_helper_page.'_helper.php');
		
	}


}
?>
