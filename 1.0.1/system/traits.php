<?php

trait Select{
	function select($table, $Limit = 0 , $Offset = 1000)
	{
		$query = $this->mysqli->query("SELECT * FROM $table LIMIT $Limit, $Offset") or exit('Error '.$this->mysqli->error);
		return $query;
	}
}

trait MkArrayObj{
	
	function mkarray($query)
	{
		if($query->num_rows == 1)
			return $query->fetch_assoc();
		
		if($query->num_rows > 1)
		{
			while($result[] = $query->fetch_assoc()){}
				array_pop($result);

			return $result;
		}
	}

	function mkobj($query)
	{
		if($query->num_rows == 1)
			return $query->fetch_object();
		
		if($query->num_rows > 1)
		{
			while($result[] = $query->fetch_object()){}
				array_pop($result);

			return $result;
		}

	}
}

?>