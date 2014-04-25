<?php
/**
* 
*/
Final class pagination
{
	
	private $start;
	private $total;
	private $first = 'First';
	private $last = 'Last';
	private $per_page;
	private $link;	
	private $base_url;
	private $begin_part = '';
	private $end_part = '';
	private $before_tag = '';
	private $after_tag = '';

	function __construct(array $array = array(''))
	{
		foreach ($array as $key => $value) {
			if(property_exists($this, $key))
				$this->$key = $value;	
		}		

		$tmp_st = explode('/', $_SERVER['REQUEST_URI']);
		$tmp_st = end($tmp_st);

		if(!is_numeric($tmp_st))
			$this->start = 1;	
		else
			$this->start = $tmp_st;

		$this->start;
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


	function show()
	{
		$tmp = (int) ($this->per_page / 2);
		
		if(($tmp < $this->total-$tmp) && ( ($this->start - $tmp) > 0) )
			$this->start = $this->start - $tmp;

		echo $this->begin_part.$this->before_tag."<a href='{$this->base_url}'>{$this->first}</a> ".$this->after_tag.$this->end_part;

		for ($list_page = $this->start; $list_page <= $this->start+$this->per_page; $list_page++) { 

			echo "{$this->before_tag}<a href='{$this->base_url}/{$list_page}'>{$list_page}</a>{$this->after_tag} ";

			if($list_page == $this->total)
				break;
		}
		$last = $this->total - (int)($this->per_page / 2);
		echo $this->begin_part.$this->before_tag."<a href='{$this->base_url}/{$last}'>{$this->last}</a>".$this->after_tag.$this->end_part;
	}
}


?>
