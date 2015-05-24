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

		$this->load 	= new loadDBClass;
		$this->session 	= new sessionClass;
		$this->cookie 	= new cookieClass;		
		$this->input 	= new inputClass;	
		$this->server 	= new serverClass;
		$this->globals 	= new globalsClass;	
	}	

	static function &get_all_instance()
	{
		return self::$all_instance;
	}
	

	
}
?>
