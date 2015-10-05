<?php
/**
* 
*/
class Main extends Ex_Controller
{
	
	function __construct()
	{
		parent::__construct();	
		//$this->load->model('main_model');
	}

	function index()
	{		
		$this->view->page('main');
	}	
	
}
?>