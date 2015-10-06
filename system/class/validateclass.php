<?php
/**
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	Validate Rules Class
*/

Final class ValidateClass
{
	private $required = 1;
	private $match = 1;
	private $email = 1;
	private $int = 1;
	private $num = 1;
	private $error = '';
	private $input = 1;
	private $field_name = 1;
	private $url = 1;
	private $min = 1;
	private $max = 1;
	private $fixed = 1;
	private $minVal = NULL;
	private $maxVal = NULL;
	private $fixedVal = NULL;
	private $trim;
	private $tag_clr;
	private $rule;

	
	function __construct()
	{
		# code...
	}

	function __call($mth_name,$mth_arg)
	{
		echo "Unknown Member Call $mth_name<br>You can get all details by calling get_class_details() method<br>".PHP_EOL;
	}

	function __get($porp_name){
		echo "Unknown Property Call $porp_name<br>You can get all details by calling get_class_details() method<br>".PHP_EOL;	
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


	function set_rules($input,$field_name,$rules,$match_val = '')
	{
		
		$this->input = $input;
		$this->field_name = $field_name;
		$rules = str_replace(' ', '', $rules);	
		$this->rule = explode('|', $rules);
		
		foreach ($this->rule as $key => $value) {
			
			if ($value === 'required') {
				$this->required($input);
			}

			if ($value === 'match') {
				$this->match($input,$match_val);
			}

			if ($value === 'email') {
				$this->email($input);
			}

			if ($value === 'int') {
				$this->int($input);
			}

			if ($value === 'num') {
				$this->num($input);
			}

			if ($value === 'url') {
				$this->url($input);
			}

			if (strpos($value,'min') !== false) {
				$this->minVal = str_replace('min', '', $value);
				$this->min($input);
			}

			if (strpos($value,'max') !== false) {
				$this->maxVal = str_replace('max', '', $value);
				$this->max($input);
			}

			if (strpos($value,'fixed') !== false) {
				$this->fixedVal = str_replace('fixed', '', $value);
				$this->fixed($input);
			}

			if ($value === 'trim') {
				$this->trim($input);
			}

			if ($value === 'tag_clr') {
				$this->tag_clr($input);
			}			
		}		
		
	}

	private function validateArray($input, $type)
	{		
		foreach ($input as $key => $value) {
			if(is_array($value))
				$this->validateArray($value,$type);
			else{
					if($type == 'required'){
						if (strlen($value) < 1) {
							$this->required = FALSE;
							$this->error .= $this->field_name." required<br>";						
						}
					}

					if($type == 'email'){
						if( filter_var($value, FILTER_VALIDATE_EMAIL))
							{
								$this->email = TRUE;
							}
						else{
								$this->email = FALSE;
								$this->error .= $this->field_name." is not valide email<br>";
							}
					}

					if($type == 'int'){
						if((filter_var($value, FILTER_VALIDATE_INT) !== FALSE) )
							{
								$this->int = TRUE;	
							}
						else{
								$this->int = FALSE;
								$this->error .= $this->field_name." must be Integer<br>";	
							}
					}

					if($type == 'num'){
						if(is_numeric($value) !== FALSE)
							{
								$this->num = TRUE;	
							}
						else{
								$this->num = FALSE;
								$this->error .= $this->field_name." must be Number<br>";	
							}
					}

					if($type == 'url'){
						if(filter_var($value, FILTER_VALIDATE_URL))
							$this->url = TRUE;	
						else{
							$this->url = FALSE;
							$this->error .= $this->field_name." must be URL<br>";	
						} 
					}					

					if($type == 'tag_clr')
					{
						$input[$key] = replace_regx(filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS));
						$this->tag_clr = TRUE;
					}

					if($type == 'min'){
						if(strlen($value) >= $this->minVal)
							{
								$this->min = TRUE;	
							}
						else{
							$this->min = FALSE;
							$this->error .= $this->field_name." length must be greater then or equal {$this->minVal} <br>";	
						}
					}

					if($type == 'max'){
						if(strlen($value) <= $this->maxVal)
							{
								$this->max = TRUE;		
							}
						else{
							$this->max = FALSE;
							$this->error .= $this->field_name." length must be less then or equal {$this->maxVal} <br>";	
						}
					}

					if($type == 'fixed'){
						if(strlen($value) <= $this->fixedVal)
							{
								$this->fixed = TRUE;	
							}
						else{
							$this->fixed = FALSE;
							$this->error .= $this->field_name." length must be equal {$this->fixedVal} <br>";	
						}
					}

					if($type == 'trim')
					{
						$input[$key] = trim($value);
						$this->trim = TRUE;
					}
			}
		}
	}


	function required($input)
	{
		if(isset($_REQUEST[$input]) || isset($_FILES[$input]))
		{			
			if(isset($_REQUEST[$input]))
			{
				if($_REQUEST[$input] != '')
					$this->required = TRUE;
				else 
				{
					$this->required == FALSE;
					$this->error .= $this->field_name." required<br>";
				}
			}
			else{
				if($_FILES[$input]['name'][0] != '')
					$this->required = TRUE;
				else 
				{
					$this->required == FALSE;
					$this->error .= $this->field_name." required<br>";
				}
			}			
		}
		else{
				$this->required = FALSE;
				$this->error .= $this->field_name." required<br>";
		}
		 
	}	

	function match($match_for, $match_with)
	{
		if (isset($_REQUEST[$match_for]) && isset($_REQUEST[$match_with]) && ($_REQUEST[$match_for] === $_REQUEST[$match_with])) 
			{
				$this->match = TRUE;				
			}
		else{
				$this->match = FALSE;
				$this->error .= $this->field_name." not match<br>";
			}
	}

	function email($input)
	{
		if (isset($_REQUEST[$input])){
			if(!is_array($_REQUEST[$input])){
				if( filter_var($_REQUEST[$input], FILTER_VALIDATE_EMAIL))
					{						
						$this->email = TRUE;						
					}
				else{
						$this->email = FALSE;
						$this->error .= $this->field_name." ".$_REQUEST[$this->input]." is not valide email<br>";
					}
			}
			else
				$this->validateArray($_REQUEST[$input],'email');
		}
		else{
			$this->email = FALSE;
			$this->error .= $this->field_name." ".$_REQUEST[$this->input]." is not valide email<br>";
		}

	}

	function int($input)
	{
		if (isset($_REQUEST[$input])){
			if(!is_array($_REQUEST[$input]))
			{
				if((filter_var($_REQUEST[$input], FILTER_VALIDATE_INT) !== FALSE) )
					{
						$this->int = TRUE;						
					}
				else{
						$this->int = FALSE;
						$this->error .= $this->field_name." must be Integer<br>";	
					}
			}
			else
				$this->validateArray($_REQUEST[$input],'int');
		}
		else{
			$this->int = FALSE;
			$this->error .= $this->field_name." must be Integer<br>";	
		}
	}

	function url($input)
	{
		if (isset($_REQUEST[$input])){
			if(!is_array($_REQUEST[$input])){
				if(filter_var($_REQUEST[$input], FILTER_VALIDATE_URL))
					{
						$this->url = TRUE;						
					}
				else{
					$this->url = FALSE;
					$this->error .= $this->field_name." must be URL<br>";	
				} 
			}
			else
				$this->validateArray($_REQUEST[$input],'url');
		}
		else{
				$this->url = FALSE;
				$this->error .= $this->field_name." must be URL<br>";	
			}
	}

	function min($input)
	{
		if (isset($_REQUEST[$input])){
			if(!is_array($_REQUEST[$input])){
				if(strlen($_REQUEST[$input]) >= $this->minVal)
					{
						$this->min = TRUE;						
					}
				else{
					$this->min = FALSE;
					$this->error .= $this->field_name." length must be greater then or equal {$this->minVal} <br>";	
				} 
			}
			else
				$this->validateArray($_REQUEST[$input],'min');
		}
		else{
				$this->min = FALSE;
				$this->error .= $this->field_name." length must be greater then or equal {$this->minVal} <br>";	
			}
	}

	function max($input)
	{
		if (isset($_REQUEST[$input])){
			if(!is_array($_REQUEST[$input])){
				if(strlen($_REQUEST[$input]) <= $this->maxVal)					
					{
						$this->max = TRUE;						
					}
				else{
					$this->max = FALSE;
					$this->error .= $this->field_name." length must be less then or equal {$this->maxVal} <br>";	
				} 
			}
			else
				$this->validateArray($_REQUEST[$input],'max');
		}
		else{
				$this->max = FALSE;
				$this->error .= $this->field_name." length must be less then or equal {$this->maxVal} <br>";	
			}
	}

	function fixed($input)
	{
		if (isset($_REQUEST[$input])){
			if(!is_array($_REQUEST[$input])){
				if(strlen($_REQUEST[$input]) == $this->fixedVal)
					{
						$this->fixed = TRUE;
					}
				else{
					$this->fixed = FALSE;
					$this->error .= $this->field_name." length must be equal {$this->fixedVal} <br>";	
				} 
			}
			else
				$this->validateArray($_REQUEST[$input],'fixed');
		}
		else{
				$this->fixed = FALSE;
				$this->error .= $this->field_name." length  must be equal {$this->fixedVal} <br>";	
			}
	}

	function num($input)
	{		
		if (isset($_REQUEST[$input])){
			if(!is_array($_REQUEST[$input]))
			{
				if(is_numeric($_REQUEST[$input])){
					$_REQUEST[$input] = trim($_REQUEST[$input]);
					$this->num = TRUE;
				}
				else{
					$this->num = FALSE;
					$this->error .= $this->field_name." must be Number<br>";
				}
			}
			else
				$_REQUEST[$input] = $this->validateArray($_REQUEST[$input],'num');
		}
	}

	function trim($input)
	{		
		if (isset($_REQUEST[$input])){
			if(!is_array($_REQUEST[$input]))
			{
				$_REQUEST[$input] = trim($_REQUEST[$input]);
				$this->trim = TRUE;
			}
			else
				$_REQUEST[$input] = $this->validateArray($_REQUEST[$input],'trim');
		}
	}


	function tag_clr($input)
	{
		if (isset($_REQUEST[$input])){
			if(!is_array($_REQUEST[$input]))
			{
				$_REQUEST[$input] = replace_regx(filter_var($_REQUEST[$input], FILTER_SANITIZE_SPECIAL_CHARS));
				$this->tag_clr = TRUE;	
			}
			else
				$_REQUEST[$input] = $this->validateArray($_REQUEST[$input],'tag_clr');
		}
	}	

	function run()
	{		
		return ($this->error == '') ? TRUE : FALSE;
	}

	function error()
	{
		return $this->error;
	}
}
?>
