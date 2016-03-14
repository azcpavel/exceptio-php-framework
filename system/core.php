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

function rqr($fileName = ""){	
	require($fileName);
}


function base_url($address = '')
{	
	return BASEPATH.$address;
}

function site_url($address = '')
{
	if(INDEXPHP === 0)
		return BASEPATH.'index.php/'.$address;
	else
		return BASEPATH.$address;
}

function redirect($link = '', $option = 1){
	
	if(INDEXPHP === 0){
		if($option)
			header("Location: ".BASEPATH.'index.php/'.$link);
		else
			header("Location: ".$link);
	}
	else{
		if($option)
			header("Location: ".BASEPATH.$link);
		else
			header("Location: ".$link);
	}
}

function form_mpt($address, $attr = array())
{
	$attr = array_replace(array('method' => 'POST'), $attr);

	foreach ($attr as $key => $value) {
    	$attr[$key] = $key.'="'.$value.'"';
    }    

	if(INDEXPHP === 0)
		echo '<form action="'.BASEPATH.'index.php/'.$address.'" '.implode(' ', $attr).' enctype="multipart/form-data">';
	else
		echo '<form action="'.BASEPATH.$address.'" '.implode(' ', $attr).' enctype="multipart/form-data">';
}

function form_spt($address, $attr = array())
{
	$attr = array_replace(array('method' => 'POST'), $attr);

	foreach ($attr as $key => $value) {
    	$attr[$key] = $key.'="'.$value.'"';
    }

	if(INDEXPHP === 0)
		echo '<form action="'.BASEPATH.'index.php/'.$address.'" '.implode(' ', $attr).' >';
	else
		echo '<form action="'.BASEPATH.$address.'" '.implode(' ', $attr).' >';
}

if(!function_exists("log_write")){

function log_write($content, $printTime = false, $fileName = "log.txt"){	
	if(!file_exists(DOCUMENT_ROOT.BASEDIR.$fileName)){
		$fh = fopen(DOCUMENT_ROOT.BASEDIR.$fileName, 'w');
		if($printTime)
			fwrite($fh, date('Y-m-d H:i:s')."# ".$content.PHP_EOL);
		else
			fwrite($fh, $content.PHP_EOL);
		fclose($fh);
	}else{
		$fh = fopen(DOCUMENT_ROOT.BASEDIR.$fileName, 'a');
		if($printTime)
			fwrite($fh, date('Y-m-d H:i:s')."# ".$content.PHP_EOL);
		else
			fwrite($fh, $content.PHP_EOL);
		fclose($fh);
	}
}

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
	}else if(count($uri) == 1 && $no == 1){
		return $uri[0];
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
		'amp'		=> '/ & /',				//Amp
		'hdoc'		=> '/"/',				//Heredoc
		'ndoc'		=> "/\'/",				//Nowdoc		
		'gt'		=> '/>/',				//Greater than
		'lt'		=> '/</',				//Less than		
		);

	$replacement = array(
		'amp'		=> ' &#38; ',			//Amp
		'hdoc'		=> '&#34;',				//Heredoc
		'ndoc'		=> '&#39;',				//Nowdoc		
		'gt'		=> '&#62;',				//Greater than
		'lt'		=> '&#60;',				//Less than		
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

	if(is_array($input)){
		foreach ($input as $key => $value) {
			if(is_array($value))
				$input[$key] = replace_regx($value, $otherRegx, $allowTags);
			else
				$input[$key] = preg_replace($regx, $replacement, $value);
		}
		return $input;
	}

	//Clean SELECT INSERT UPDATE DELETE UNION
	$input = preg_replace_callback(array('/\b(select)\b/i','/\b(insert)\b/i','/\b(update)\b/i','/\b(delete)\b/i','/\b(union)\b/i'),
			function($matches){

				$regxInd = array(
					'a' => '/a/',
					'c' => '/c/',
					'd' => '/d/',
					'e' => '/e/',
					'i' => '/i/',
					'l' => '/l/',
					'n' => '/n/',
					'o' => '/o/',
					'p' => '/p/',
					'r' => '/r/',
					's' => '/s/',
					't' => '/t/',
					'u' => '/u/',
					'A'	=> '/A/',
					'C' => '/C/',
					'D' => '/D/',
					'E' => '/E/',
					'I' => '/I/',
					'L' => '/L/',
					'N' => '/N/',
					'O' => '/O/',
					'P' => '/P/',
					'R' => '/R/',
					'S' => '/S/',
					'T' => '/T/',
					'U' => '/U/',
					);

				$replacementVal = array(
					'a' => '&#97;',
					'c' => '&#99;',
					'd' => '&#100;',
					'e' => '&#101;',
					'i' => '&#105;',
					'l' => '&#108;',
					'n' => '&#110;',
					'o' => '&#111;',
					'p' => '&#112;',
					'r' => '&#114;',
					's' => '&#115;',
					't' => '&#116;',
					'u' => '&#117;',
					'A'	=> '&#65;',
					'C' => '&#67;',
					'D' => '&#68;',
					'E' => '&#69;',
					'I' => '&#73;',
					'L' => '&#76;',
					'N' => '&#78;',
					'O' => '&#79;',
					'P' => '&#80;',
					'R' => '&#82;',
					'S' => '&#83;',
					'T' => '&#84;',
					'U' => '&#85;',
					);
				return preg_replace($regxInd, $replacementVal, $matches[0]);				
			}
		, $input);

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


function include_try($cont_func, $cont_param_arr, $output = false) {	
    // Setup shutdown function:
    static $run = 0;
    if($run++ === 0) register_shutdown_function('include_shutdown_handler');

    // If output is not allowed, capture it:
    if(!$output) ob_start();
    // Reset error_get_last():
    @user_error('error_get_last mark');
    // Enable shutdown handler and store parameters:
    $params = array($cont_func, $cont_param_arr, $output, getcwd());
    $GLOBALS['_include_shutdown_handler'] = $params;
}

function include_catch() {
    $error_get_last = error_get_last();
    $output = $GLOBALS['_include_shutdown_handler'][2];
    // Disable shutdown handler:
    $GLOBALS['_include_shutdown_handler'] = NULL;
    // Check unauthorized outputs or if an error occured:
    return ($output ? false : ob_get_clean() !== '')
        || $error_get_last['message'] !== 'error_get_last mark';
}

function include_shutdown_handler() {
    $func = $GLOBALS['_include_shutdown_handler'];
    if($func !== NULL) {
        // Cleanup:
        include_catch();
        // Fix potentially wrong working directory:
        chdir($func[3]);
        // Call continuation function:
        call_user_func_array($func[0], $func[1]);
    }
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

function get_range_dates($argDateFrom, $argDateTo, $format = 'Y-m-d')
{
	// date args must be in YYYY-mm-dd format
	// takes two dates and creates an inclusive array of the dates between the from and to dates.

	$dateArray=array();

	$dateFrom=mktime(1, 0, 0, substr($argDateFrom,5,2), substr($argDateFrom,8,2), substr($argDateFrom,0,4));
	$dateTo=mktime(1, 0, 0, substr($argDateTo,5,2), substr($argDateTo,8,2), substr($argDateTo,0,4));

	if ($dateTo >= $dateFrom)
	{
		array_push($dateArray,date($format,$dateFrom)); // first entry
		while ($dateFrom < $dateTo)
		{
			$dateFrom += 86400; // add 24 hours
			array_push($dateArray,date($format,$dateFrom));
		}
	}
	return $dateArray;
}


function url_wrap($string = '', $class = '', $target = '_blank'){
	// The Regular Expression filter
	$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/i";
	// Check if there is a url in the text
	if(preg_match($reg_exUrl, $string, $url)) {

	       // make the urls hyper links
	       return preg_replace($reg_exUrl, "<a class='$class' href='{$url[0]}' target='$target'>{$url[0]}</a> ", $string);

	} else {

	       // if no urls in the text just return the text
	       return $string;

	}	
}


function change_case($string = '', $case = 'C'){
	if($case == 'P' || $case == 'p')
		return ucfirst(preg_replace_callback('/_./', function($match){
			return preg_replace_callback('/\w/', function($matchSub){ 				
				return ucwords($matchSub[0]);
			}, $match[0][1]);
			
		}, $string));
	else if($case == 'C' || $case == 'c')
		return lcfirst(preg_replace_callback('/_./', function($match){
			return preg_replace_callback('/\w/', function($matchSub){ 				
				return ucwords($matchSub[0]);
			}, $match[0][1]);
			
		}, $string));
	else
		return preg_replace_callback('/[A-Z]/', function($match){
			return '_'.lcfirst($match[0]);			
		}, lcfirst($string));
}

if(!function_exists('look')){
	function look($array, $print_r = 1, $exit = 1){
		echo "<pre>";
		echo PHP_EOL."=========================".PHP_EOL;
		if($print_r == 1) print_r($array); else var_dump($array);
		echo PHP_EOL."=========================".PHP_EOL;
		echo "</pre>";

		if($exit)
			exit();
	}
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
