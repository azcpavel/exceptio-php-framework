<?php

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

	protected function __construct()
	{
		self::$all_instance =& $this;					

		$this->load 	= new loadClass;
		$this->view 	= new viewClass;
		$this->file 	= new fileClass;
		$this->session 	= new sessionClass;
		$this->input 	= new inputClass;
		$this->server 	= new serverClass;
		$this->globals 	= new globalsClass;
		$this->validate = new validateClass;

		if(NOCACHE==1)
			include(SYSTEM.'/no_cache.php');
	}
	

	static function &get_all_instance()
	{
		return self::$all_instance;
	}
	
}


?>
