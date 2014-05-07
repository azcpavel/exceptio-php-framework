<?php
/*
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
	private $error = '';
	private $post = 1;
	private $field_name = 1;
	private $url = 1;
	private $trim;
	private $tag_clr;
	private $rule;

	
	function __construct()
	{
		# code...
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


	function set_rules($post,$field_name,$rules,$match_val = '')
	{
		
		$this->post = $post;
		$this->field_name = $field_name;
		$rules = str_replace(' ', '', $rules);	
		$this->rule = explode('|', $rules);

		foreach ($this->rule as $key => $value) {
			
			if ($value === 'required') {
				$this->required($post);
			}

			if ($value === 'match') {
				$this->match($post,$match_val);
			}

			if ($value === 'email') {
				$this->email($post);
			}

			if ($value === 'int') {
				$this->int($post);
			}

			if ($value === 'url') {
				$this->url($post);
			}

			if ($value === 'trim') {
				$this->trim($post);
			}

			if ($value === 'tag_clr') {
				$this->tag_clr($post);
			}
		}		
		
	}


	function required($post)
	{
		if ((isset($_REQUEST[$post]) && $_REQUEST[$post] != '') || (@$_FILES[$post]['name'][0] != '')) {
			
			$this->required = ($this->required != FALSE ) ? TRUE : FALSE;
		}
		else{
				$this->required = FALSE;
				$this->error .= $this->field_name." required<br>";
			}
		 
	}

	function match($match_for, $match_with)
	{
		if (isset($_REQUEST[$match_for]) && isset($_REQUEST[$match_with]) && ($_REQUEST[$match_for] === $_REQUEST[$match_with])) 
			$this->match = ($this->match != FALSE ) ? TRUE : FALSE; 
		else{
				$this->match = FALSE;
				$this->error .= $this->field_name." not match<br>";
			}
	}

	function email($post)
	{
		if (isset($_REQUEST[$post]) && filter_var($_REQUEST[$post], FILTER_VALIDATE_EMAIL))
			$this->email = ($this->email != FALSE ) ? TRUE : FALSE; 
		else{
				$this->email = FALSE;
				$this->error .= $this->field_name." ".@$_REQUEST[$this->post]." is not valide email<br>";
			}
	}

	function int($post)
	{
		if (isset($_REQUEST[$post]) && (filter_var($_REQUEST[$post], FILTER_VALIDATE_INT) !== FALSE) )
			$this->int = ($this->int != FALSE ) ? TRUE : FALSE; 
		else{
				$this->int = FALSE;
				$this->error .= $this->field_name." must be Integer<br>";	
			}
	}

	function url($post)
	{
		if (isset($_REQUEST[$post]) && (filter_var($_REQUEST[$post], FILTER_VALIDATE_URL)) )
			$this->url = ($this->url != FALSE ) ? TRUE : FALSE; 
		else{
				$this->url = FALSE;
				$this->error .= $this->field_name." must be Url<br>";	
			}
	}

	function trim($post)
	{
		@$_REQUEST[$post] = trim(@$_REQUEST[$post]);
		$this->trim = TRUE;
	}

	function tag_clr($post)
	{
		@$_REQUEST[$post] = filter_var(@$_REQUEST[$post], FILTER_SANITIZE_SPECIAL_CHARS);
		$this->tag_clr = TRUE;	
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
