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
	if(count($uri) > 1)
		return $uri[$no-1];
}

function truncate_str($str, $maxlen) {
	if ( strlen($str) <= $maxlen ) return $str;
		$newstr = substr($str, 0, $maxlen);
		
	if ( substr($newstr,-1,1) != ' ' )
		$newstr = substr($newstr, 0, strrpos($newstr, " "));
	return $newstr;
}

function print_thousand($num, $dec = 2)
	{
		if($num > 3)
		{
			$last_part = substr($num, -3, 3);
			$first_part = substr($num, 0, -3);
			$final = '';
			$final_first = '';
			
			if(strlen($first_part) > $dec*3)
			{
				$final_first = substr($first_part, 0, -$dec*3).',';
				$first_part = substr($first_part, -$dec*3, $dec*3);

			}		

			$array = str_split($first_part);

			$flage = 0;
			$count = count($array);				

			$interval = 1;
			$count_coma = 1;
			$final = ','.$last_part;
			for ($i=$count-1; $i >= 0; $i--) { 
				$final = $array[$i].$final;
				
					if($count_coma == $dec && $interval <= 3)
						{
							$final = ','.$final;
							$interval ++;
							$count_coma=0;
						}
					$count_coma++;

			}

			$tmp_first = str_split($final);
			if($tmp_first[0]==',')
				$final = substr($final, 1);
			return $final_first.$final;
		}
		else
			return $num;
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
