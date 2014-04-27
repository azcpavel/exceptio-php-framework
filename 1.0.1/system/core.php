<?php


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
	return BASEPATH.'index.php/'.$address;
}

function redirect($link){
	
	header("Location: ".BASEPATH.'index.php/'.$link);
}

function form_mpt($address)
{
	echo '<form action="'.BASEPATH.'index.php/'.$address.'" method="POST" enctype="multipart/form-data">';
}

function form_spt($address)
{
	echo '<form action="'.BASEPATH.'index.php/'.$address.'" method="POST">';
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
