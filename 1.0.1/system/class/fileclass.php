<?php
/*
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	File Class
*/

Final class FileClass
{
	
	private $upload_dir;
	private $upload_file_name;
	private $upload_file_temp_name;
	private $upload_max_size = 0;
	private $upload_file_type;
	private $upload_file_source;
	private $upload_create_thumb;
	private $upload_image_ratio;
	private $upload_max_width = 0;
	private $upload_max_height = 0;
	private $upload_data;
	private $upload_error;
	private $upload_error_ok = 0;	

	
	function __construct()
	{
		
	}

	function __call($mth_name,$mth_arg)
	{
		echo "Unknown Member Call $mth_name<br>You can get all details by calling get_class_details() method";
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

	function config_upload(array $config)
	{
		foreach ($config as $key => $value) {
			
			if($key == 'upload_max_size')
				$value = $value * 1024;

			if(property_exists($this, $key))
				$this->$key = $value;
		}		
		
		$mmtp = array_flip(explode('|', $this->upload_file_type));

		global $mimes_file_types;		

		$mmtp = array_intersect_key($mimes_file_types,$mmtp);
		$mmtp_full = '';
		foreach ($mmtp as $key => $value) {
			
			if(is_array($value))
				$mmtp_full .= implode(',', $value).',';
			else
				$mmtp_full .= $value.',';	
		}
		
		$this->upload_file_type = substr($mmtp_full,0,-1);
		
	}

	function do_upload($post_name)
	{
		$upload_data  = array();
		$upload_error = '';
		// echo "<pre>";
		// print_r($_FILES[$post_name]['error'][0]);exit;
		if(isset($_FILES[$post_name]['error'][0])){		
		foreach ($_FILES[$post_name]['error'] as $key => $value) {		
			
			$tmp_name 			= $_FILES[$post_name]['tmp_name'][$key];
			$file_name 			= $_FILES[$post_name]['name'][$key];
			$file_client_name 	= str_replace(' ','_',date('YmdHis').rand().'_'.$file_name);
			$file_size 			= number_format(($_FILES[$post_name]['size'][$key] / 1024),2);
			$file_type 			= $_FILES[$post_name]['type'][$key];

			global $mimes_file_types;
			$tmp_file_type = $mimes_file_types;			

			foreach ($tmp_file_type as $key_file_type => $value_file_type) {
				
				if(is_array($value_file_type)){
					foreach ($value_file_type as $key_file_type_sub => $value_file_type_sub) {
						
						if ($value_file_type_sub === $file_type) {
					
							$file_type = $key_file_type;
							break;
						}
					}
				}
				else
				{
					if ($value_file_type === $file_type) {
					
						$file_type = $key_file_type;
						break;
					}
				}

			}
						
			list($width, $height, $type, $attr) = @getimagesize($tmp_name);

			// print_r(get_object_vars($this));exit;
			$file_type_check = @strpos($this->upload_file_type, $_FILES[$post_name]['type'][$key]);

			if ($value == UPLOAD_ERR_OK){	
				
				if($file_type_check !== FALSE){

					if($this->upload_max_size === 0 || ($_FILES[$post_name]['size'][$key] <= $this->upload_max_size)){						

						if($this->upload_max_width === 0 || $width <= $this->upload_max_width){

							if($this->upload_max_height === 0 || $height <= $this->upload_max_height)

								$this->upload_error_ok = 1;

							else
								$upload_error .= "<br>Please Check Upload Max Height for $file_name";
						}
						else
							$upload_error .= "<br>Please Check Upload Max Width for $file_name";				        
				    }
				    else
				    	$upload_error .= "<br>$file_name Too Large Maximum ".($this->upload_max_size / 1024).' Kb';
		    	}
		    	else		    		
		    		$upload_error .= "<br>$file_name Type Not Allowd";		    	
			}
			else
				$upload_error .= "<br>$file_name Upload Error";
		}

		
		if($this->upload_error_ok === 1)
		foreach ($_FILES[$post_name]['error'] as $key => $value) {		
			
			$tmp_name 			= $_FILES[$post_name]['tmp_name'][$key];
			$file_name 			= $_FILES[$post_name]['name'][$key];
			$file_client_name 	= str_replace(' ','_',date('YmdHis').rand().'_'.$file_name);
			$file_size 			= number_format(($_FILES[$post_name]['size'][$key] / 1024),2);
			$file_type 			= $_FILES[$post_name]['type'][$key];

			global $mimes_file_types;
			$tmp_file_type = $mimes_file_types;			

			foreach ($tmp_file_type as $key_file_type => $value_file_type) {
				
				if(is_array($value_file_type)){
					foreach ($value_file_type as $key_file_type_sub => $value_file_type_sub) {
						
						if ($value_file_type_sub === $file_type) {
					
							$file_type = $key_file_type;
							break;
						}
					}
				}
				else
				{
					if ($value_file_type === $file_type) {
					
						$file_type = $key_file_type;
						break;
					}
				}

			}			
						if(@move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'].$this->upload_dir.$file_client_name))
				        {
				        	$upload_data[] = array(
				        						'file_name' => $file_name,
				        						'file_path' => $this->upload_dir,				        						
				        						'file_client_name' => $file_client_name,
				        						'file_type' => $file_type,
				        						'file_size_kb' => $file_size,
				        						'file_full_path' => $this->upload_dir.$file_client_name,
				        						'file_server_dir' => $_SERVER['DOCUMENT_ROOT'].$this->upload_dir,
				        						'file_server_path' => $_SERVER['DOCUMENT_ROOT'].$this->upload_dir.$file_client_name,
				        						'file_web_url' => 'http://'.$_SERVER['HTTP_HOST'].$this->upload_dir.$file_client_name,
				        						);			        
				        }
				        else
				        	$upload_error .= "<br>Please Check Upload Document ROOT for $file_name";
				        
			}
			
		}
		else
			$upload_error .= "<br>You Must Use Form Name As Array<br>Ex. name='your_post_name[]' ";

		
		$this->upload_data  = $upload_data;
		$this->upload_error = $upload_error;		
	}

	function mk_download($address)
	{
		if (file_exists($address))
			return file_get_contents($address);
		else
			die ("File Not Found in /$address");
	}

	function do_download($download_name ='' , $data_name = '')
	{
		

		$file_ex = explode('.',$download_name);
		$file_ex =  end($file_ex);
		// echo "$file_ex";exit;

		global $mimes_file_types;
		global $mime_file_types_unknown;
		
		if(array_key_exists($file_ex,$mimes_file_types))
			$mime = is_array($mimes_file_types[$file_ex]) ? $mimes_file_types[$file_ex][0] : $mimes_file_types[$file_ex];
		else
			$mime = $mime_file_types_unknown;		

		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE)
		{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$download_name.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".strlen($data_name));
		}
		else
		{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$download_name.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".strlen($data_name));
		}

		die($data_name);

	}

	function upload_data()
	{
		return $this->upload_data;
	}

	function upload_error()
	{
		return $this->upload_error;
	}
	
}
?>
