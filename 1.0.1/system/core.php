<?php
/*
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	Core functions
*/

function __autoload($class)
{
	if(file_exists(DOCUMENT_ROOT.BASEDIR.APPLICATION.'/controllers/'.$class.'.php'))	
		require(APPLICATION.'/controllers/'.$class.'.php');	

	if(file_exists(DOCUMENT_ROOT.BASEDIR.APPLICATION.'/helpers/'.$class.'.php'))	
		require(APPLICATION.'/helpers/'.$class.'.php');
}


function base_url()
{	
	return BASEPATH;
}

function site_url($address = '')
{
	if(INDEXPHP === 0)
		return BASEPATH.'index.php/'.$address;
	else
		return BASEPATH.$address;
}

function redirect($link){
	
	if(INDEXPHP === 0)
		header("Location: ".BASEPATH.'index.php/'.$link);
	else
		header("Location: ".BASEPATH.$link);
}

function form_mpt($address)
{
	if(INDEXPHP === 0)
		echo '<form action="'.BASEPATH.'index.php/'.$address.'" method="POST" enctype="multipart/form-data">';
	else
		echo '<form action="'.BASEPATH.$address.'" method="POST" enctype="multipart/form-data">';
}

function form_spt($address)
{
	if(INDEXPHP === 0)
		echo '<form action="'.BASEPATH.'index.php/'.$address.'" method="POST">';
	else
		echo '<form action="'.BASEPATH.$address.'" method="POST">';
}

function uri_segment($no)
{
	$uri = BASEHOST.$_SERVER['REQUEST_URI'];	
	$uri = str_replace(BASEPATH ,'', $uri);	
	$uri = explode('/', $uri);
	return $uri[$no-1];
}

function &get_controller_instance()
{
	return ex_controller::get_all_instance();
}

function &get_model_instance()
{
	return ex_model::get_all_instance();
}

function &get_view_instance()
{
	return ViewClass::get_all_instance();
}
?>
