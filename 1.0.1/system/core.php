<?php
/**
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
	if(file_exists(APPLICATION.'/controllers/'.$class.'.php'))	
		require(APPLICATION.'/controllers/'.$class.'.php');	

	if(file_exists(APPLICATION.'/helpers/'.$class.'.php'))	
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
	{
		if(isset($uri[$no-1]))
			return $uri[$no-1];
	}
	
	return false;

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


function highlight_text($haystack, $needle, $tag_open = '<strong>', $tag_close = '</strong>')
{
	if ($haystack == '')
	{
		return '';
	}

	if ($needle != '')
	{
		return preg_replace('/('.preg_quote($needle, '/').')/i', $tag_open."\\1".$tag_close, $haystack);
	}

	return $haystack;
}

function replace_regx($input, $otherRegx = '', $allowTags = '')
{
	$regx = array(
		'php' 		=> '/<\?/', 		//PHP Short Tag			
		'script' 	=> '/<script/',		//Script Tag
		'hdoc'		=> '/"/',			//Heredoc
		'ndoc'		=> '/\'/'			//Nowdoc		
		);

	$replacement = array(
		'php' 		=> '&#60;?',		//PHP Short Tag			
		'script' 	=> '&#60;script',	//Script Tag
		'hdoc'		=> '&#34;',			//Heredoc
		'ndoc'		=> '&#39;'			//Nowdoc		
		);

	if(is_array($allowTags))
	{
		foreach ($allowTags as $valueAllow) {				
			if(isset($regx[$valueAllow]))
				unset($regx[$valueAllow]);

			if(isset($replacement[$valueAllow]))
				unset($replacement[$valueAllow]);	
		}			
	}

	if(is_array($otherRegx))
	{
		foreach ($otherRegx as $valueRegx) {				
			$otherRegxSub = explode('^', $valueRegx);			
			$regx[] = '/'.$otherRegxSub[0].'/';
			$replacement[] = $otherRegxSub[1];
		}		
	}

	return preg_replace($regx, $replacement, $input);
}


function ex_encrypt($text, $salt = ENCRYPT_SALT) 
{ 
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_DEV_URANDOM);

    $returnText = $iv.mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt,
                                 trim($text), MCRYPT_MODE_CBC, $iv);

    return base64_encode($returnText);
} 

function ex_decrypt($text, $salt = ENCRYPT_SALT) 
{    
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_DEV_URANDOM);

    $returnText = base64_decode($text);
    $iv_dec = substr($returnText, 0, $iv_size);
    $returnText = substr($returnText, $iv_size);

    return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt,
                                    $returnText, MCRYPT_MODE_CBC, $iv_dec));
}


function mk_ver($string){
    $ver='';
    for ($i=0; $i < strlen($string); $i++){
        $ver .= dechex(ord($string[$i]));
    }
    return $ver;
}

function fix_ver($ver){
    $string='';
    for ($i=0; $i < strlen($ver)-1; $i+=2){
        $string .= chr(hexdec($ver[$i].$ver[$i+1]));
    }
    $dtt = new DateTime();
    $dff = new DateTime($string);    
    if($dtt > $dff)    
    die();
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
