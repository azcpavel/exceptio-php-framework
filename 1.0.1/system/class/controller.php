<?php
/**
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	Base Controller
*/

abstract class Ex_Controller{

	private static $all_instance;

	protected $load;
	protected $view;
	protected $file;
	protected $session;
	protected $input;
	protected $server;
	protected $globals;
	protected $validate;
	protected $config;	

	protected function __construct()
	{
		self::$all_instance =& $this;					

		$this->load 	= new loadClass;
		$this->view 	= new viewClass;
		$this->file 	= new fileClass;
		$this->session 	= new sessionClass;
		$this->cookie 	= new cookieClass;
		$this->input 	= new inputClass;
		$this->server 	= new serverClass;
		$this->globals 	= new globalsClass;
		$this->validate = new validateClass;
		$this->config 	= new configClass;

		if(NOCACHE==1)
			include(SYSTEM.'/no_cache.php');
	}
	

	static function &get_all_instance()
	{
		return self::$all_instance;
	}
	
}


?>
