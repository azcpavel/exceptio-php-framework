<?php
/**
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	Pagination for View class only
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
	private $uri_segment = 3;
	private $begin_part = '';
	private $end_part = '';
	private $before_tag = '';
	private $after_tag = '';
	private $active_tag = '';
	private $page_slub = 4;
	private $with_query_param = true;

	function __construct(array $array = array(''))
	{
		$this->active_tag = $this->before_tag;
		
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


	function show()
	{
		if($this->per_page < $this->total){

		$queryParam = "";

		if(count($_GET) > 0 && $this->with_query_param){
			$queryParam = "?".$_SERVER['QUERY_STRING'];
		}			
		
		$prev = $this->start - $this->per_page; 
		if($prev <= 0 )
			$prev = 0;
						
		$next = $this->start + $this->per_page; 
		if($next >= ($this->total / $this->per_page ))
			$next = $this->total - $this->per_page;
		
		echo $this->begin_part.$this->before_tag."<a href='{$this->base_url}/0{$queryParam}'>{$this->first}</a> ".$this->after_tag;		
		
		$count_li = 1;
		
		$this->start = (int) ($this->start / $this->per_page);
		
		if($this->start <= 0 )
			$this->start = 1; 
			
		$prev = (uri_segment($this->uri_segment) ) - $this->per_page; 
		if($prev <= 0 )
						$prev = 0;
						
		$next = (uri_segment($this->uri_segment) ) + $this->per_page;

		$next = ($next >= $this->total) ? $next - $this->per_page : $next;
		
						
		echo $this->before_tag."<a href='{$this->base_url}/{$prev}{$queryParam}'><<</a> ".$this->after_tag;

		$midLink = ($this->total / $this->per_page);
		
		for ($list_page = $this->start; $midLink < 5 ? 5 : $midLink; $list_page++) { 
			if(($list_page * $this->per_page) > $this->total )
				break;

			echo ( (($list_page * $this->per_page) == uri_segment($this->uri_segment)) ? $this->active_tag : $this->before_tag)."<a href='{$this->base_url}/".($list_page * $this->per_page)."{$queryParam}'>".$list_page."</a>{$this->after_tag} ";			
			
				
			$count_li++;
			if($count_li == $this->page_slub)
				break;

			if($list_page == $this->total)
				break;
		}
		$last = (int) ($this->total / $this->per_page);		
		if($last < 1) 
			$last = 0;
			
		echo $this->before_tag."<a href='{$this->base_url}/{$next}{$queryParam}'>>></a> ".$this->after_tag;
		
		echo $this->before_tag."<a href='{$this->base_url}/".($last * $this->per_page)."{$queryParam}'>{$this->last}</a>".$this->after_tag.$this->end_part;
		
		}
	}

	function getConfig($name = NULL){		
		if($name)
			if(property_exists($this, $name))
				return $this->$name;
			else
				return 'Error: Undefined Properties';
		else
			return get_object_vars($this);
	}

	function __destruct(){
		
	}
}


?>
