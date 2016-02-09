<?php
/**
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	Main View Class, Page & View library loader
*/

Final class ViewClass
{
	protected $session;
	protected $input;
	protected $server;
	protected $globals;
	protected $ob_level;
	
	function __construct()
	{
		$this->session 	= new sessionClass;
		$this->cookie 	= new cookieClass;
		$this->input 	= new inputClass;
		$this->server 	= new serverClass;
		$this->globals 	= new globalsClass;
		$this->ob_level = ob_get_level();		
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


	function page($load_view_page = 'main', array $data_for_view = array(), $outputBuffer = false)
	{		
		ob_start();
		extract($data_for_view);		

		if($outputBuffer && file_exists(APPLICATION.'/views/'.$load_view_page.'.php')){						
			include (APPLICATION.'/views/'.$load_view_page.'.php');
			$return = ob_get_contents();
			@ob_end_clean();						
			return $return;
		}
		else if(file_exists(APPLICATION.'/views/'.$load_view_page.'.php'))
			include (APPLICATION.'/views/'.$load_view_page.'.php');
		else
			exit("View not found in your application/views/".$load_view_page.'.php');

		if(ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}		
		
	}

	function library($load_libraries_name = '',$config = '')
	{		

		$base_name = basename($load_libraries_name);

		if(file_exists(APPLICATION.'/libraries/'.$load_libraries_name.'.php'))
			{
				if(!class_exists($base_name))
					require(APPLICATION.'/libraries/'.$load_libraries_name.'.php');
				if(class_exists($base_name))
					{						
						$this->$base_name = new $base_name($config);
					}
				else
					exit("Class $base_name not found in your application/libraries/".$load_libraries_name.'.php');
			}
		else
			exit("Libraries not found in your application/libraries/".$load_libraries_name.'.php');
	}
	
	function pagination(array $config = array(''))
	{
		require(SYSTEM.'/class/pagination.php');
		$this->pagination = new pagination($config);
	}

	function __destruct(){
		
	}
	

}

?>
