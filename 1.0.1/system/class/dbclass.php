<?php
Final class DbClass
{
	private $mysqli;
	private $select 	= '*';	
	private $table;
	private $join 		= '';
	private $where 		= 1;
	private $order_by 	= '';
	private $group_by 	= '';
	private $limit 		= '';
	private $query;
	private $query_str;
	private $error_query;
	private $affected_rows;
	private $db_name;

	
	function __construct($driver,$host,$uname,$pass,$db,$port,$service,$protocol,$server,$uid)
	{
		if($driver == 'mysql' || $driver == 'mysqli')
			$dsn = "mysql:host=$host;port=$port;dbname=$db";

		elseif($driver == 'cubrid')
			$dsn = "cubrid:dbname=$db;host=$host;port=$post";

		elseif($driver == 'firebird')
			$dsn = "firebird:dbname=$host/port:$port$db";

		elseif($driver == 'ibm')
			$dsn = "ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$db;HOSTNAME=$host;PORT=$port;PROTOCOL=$protocol;";

		elseif($driver == 'informix')
			$dsn = "informix:host=$host;service=$service;database=$db;server=$server; protocol=$protocol;EnableScrollableCursors=1";

		elseif($driver == 'oci')
			$dsn = "oci:dbname=//$host:$port/$db";

		elseif($driver == 'sqlsrv')
			$dsn = "sqlsrv:Server=$host,$port;Database=$db";		

		elseif($driver == 'odbc')
			{
				if (!file_exists($db)) {
				    die("Could not find database file in $db");
				}

				$dsn = "odbc:DRIVER={Microsoft Access Driver (*.mdb, *.accdb)}; DBQ=$db; Uid=$uname; Pwd=$pass;";
			}

		elseif($driver == 'pgsql')
			$dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass";		

		elseif($driver == '4D')
			$dsn = "4D:host=$host;charset=UTF-8";


		try{

			$this->pdo = @new pdo($dsn,$uname,$pass);
			$this->db_name = $db;	

		} catch (PDOException $e) {
		    print "Error!: " . $e->getMessage() . "<br/>";
		    die();
		}

		// if($this->pdo->connect_error)
		// 	exit('Connection Error No: '.$this->pdo->connect_errno.'<br>Connection Error Cd: '.$this->pdo->connect_error);

		//$this->pdo->select_db($db);
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

	function query($query = "")
	{
		if ($query === "")
			$query = "SELECT {$this->select} FROM {$this->table} {$this->join} WHERE {$this->where} {$this->order_by} {$this->group_by} {$this->limit}";

		$this->query_str = $query;
		$this->query = $this->pdo->query($query) or $error_t = $this->pdo->errorInfo();		

		if(isset($error_t) && $error_t[1] != '')
		{
			exit('Error No: '.$error_t[1].'<br>Error Co: '.$error_t[2]."<br> $query");
		}
		
		$this->order_by = $this->join = $this->limit = '';
		$this->where = 1;
		$this->select = '*';
		
		return $this;
	}

	function get($table = 0, $offset = 0, $limit = 0)
	{
		$this->table = (($table !== 0) ? $table : $this->table);		

		if($limit != 0 || $offset !=0)
		{
			$this->limit = "LIMIT $offset";

			if($limit !=0 )
				$this->limit .= ", $limit"; 
		}		$error_t = $this->pdo->errorInfo();

		$this->query();
		
		return $this;
	}

	function get_where($table = 0, $where = 1, $offset =0, $limit = 0)
	{
		$this->table = (($table !== 0) ? $table : $this->table);		

		if($limit != 0 || $offset !=0)
		{
			$this->limit = "LIMIT $offset";

			if($limit !=0 )
				$this->limit .= ", $limit"; 
		}

		$this->where($where);

		$this->query();

		return $this;
	}

	function select($select = '*')
	{
		$this->select = $select;

		return $this;
	}

	function select_max($MAX, $as = 0)
	{
		$this->select = ($as === 0 ) ? "MAX($MAX) as $MAX" : "MAX($MAX) as $as";

		return $this;
	}

	function select_MIN($MIN, $as = 0)
	{
		$this->select = ($as === 0 ) ? "MIN($MIN) as $MIN" : "MIN($MIN) as $as";

		return $this;
	}

	function select_AVG($AVG, $as = 0)
	{
		$this->select = ($as === 0 ) ? "AVG($AVG) as $AVG" : "AVG($AVG) as $as";

		return $this;
	}	

	function from($table)
	{
		$this->table = $table;

		return $this;
	}

	function join($table, $join, $pos = 0)
	{
		$this->join .=  (($pos !== 0) ? "{strtoupper($pos)} JOIN $table ON $join " : "JOIN $table ON $join ");

		return $this;
	}

	function group_by($group_by = '')
	{
		$this->group_by = 'GROUP BY '.$group_by;

		return $this;
	}

	function where($where = 1)
	{
		$where_full = '';

		if(is_array($where)){
			foreach ($where as $key => $value) {

				if( preg_match('/<|>|\=|!| LIKE| BETWEEN| IN| NOT IN/', $key) && (preg_match('/ AND$| OR$|\'|^\(|\)$/', $value)) )					
					$where_full .= " $key $value";
				elseif(preg_match('/<|>|\=|!/', $key))					
					$where_full .= " $key '$value' AND";
				elseif (preg_match('/ AND$| OR$|^\(|\)$/', $value))
					$where_full .= " $key = $value";
				else
					$where_full .= " $key = '$value' AND";			}
			
			if(substr($where_full,-4) === ' AND')
				$this->where = substr($where_full, 0, -4);
			else
				$this->where = $where_full;		 
		}
		else
			$this->where = $where;

		return $this;
	}

	function order_by($order_by = 0, $order = 0)
	{
		
			if($this->order_by == '')
				$this->order_by = "ORDER BY $order_by";
			else
				$this->order_by .= " ,$order_by";
		
			$this->order_by .= " $order";

		return $this;
	}

	function limit($offset = 0, $limit = 0)
	{
		if($limit != 0 || $offset !=0)
		{
			$this->limit = "LIMIT $offset";

			if($limit !=0 )
				$this->limit .= ", $limit"; 
		}

		return $this;
	}

	function num_rows()
	{
		$num = $this->pdo->query($this->query_str) or $error_t = $this->pdo->errorInfo();		

		if(isset($error_t) && $error_t[1] != '')
		{
			exit('Error No: '.$error_t[1].'<br>Error Co: '.$error_t[2]."<br> $query");
		}
		$num = count($num->fetchAll());				
		return $num;
	}


	function row_array()
	{		
		return $this->query->fetch(PDO::FETCH_ASSOC);		
	}

	function result_array()
	{		
		while($result[] = $this->query->fetch(PDO::FETCH_ASSOC)){}
			array_pop($result);

		return $result;		
	}

	function row()
	{		
		return $this->query->fetch(PDO::FETCH_OBJ);
	}

	function result()
	{			
		while($result[] = $this->query->fetch(PDO::FETCH_OBJ)){}
			array_pop($result);

		return $result;		
	}

	function fetch_column($limit = 0)
	{
		return $this->query->fetchColumn($limit);		
	}

	function show_column($table)
	{
		return $this->query("SHOW COLUMNS FROM $table");		
	}

	function show_tables()
	{
		return $this->query("SHOW TABLES FROM {$this->db_name}");		
	}


	function insert($table, $values = 1)
	{

		$values_full = '(';
		$key_full = '';
		
		if(is_array($values)){
			$key_full = '(';
			foreach ($values as $key => $value) {				
				
					$values_full .= "'$value',";
					$key_full .= "$key,";
			}

			$values_full = substr($values_full, 0, -1).')';
			$key_full = substr($key_full, 0, -1).')';
		}
		else
			$values_full .= $values.')';
	
	
		$this->affected_rows = $this->pdo->exec("INSERT INTO {$table} $key_full VALUES {$values_full}"); 
		
		$error_t = $this->pdo->errorInfo();
		if($error_t[1] != '')
		{
			exit('Error No: '.$error_t[1].'<br>Error Co: '.$error_t[2]."<br> INSERT INTO {$table} $key_full VALUES {$values_full}");
		}

		return $this;

	}

	function update($table, $values = '')
	{		
		$values_full = '';

		if(is_array($values)){
			foreach ($values as $key => $value) {				
				
					$values_full .= " $key = '$value',";
			}

			$values_full = substr($values_full, 0, -1);
		}
		else
			$values_full = $values;

		$this->affected_rows = $this->pdo->exec("UPDATE $table SET$values_full WHERE {$this->where} ");

		$error_t = $this->pdo->errorInfo();
		if($error_t[1] != '')
		{
			exit('Error No: '.$error_t[1].'<br>Error Co: '.$error_t[2]."<br> UPDATE $table SET$values_full WHERE {$this->where} ");
		}

		return $this;
	}


	function delete($table, $where = 1)
	{		

		$this->where($where);

		$this->affected_rows = $this->pdo->exec("DELETE FROM $table WHERE {$this->where}");

		$error_t = $this->pdo->errorInfo();
		if($error_t[1] != '')
		{
			exit('Error No: '.$error_t[1].'<br>Error Co: '.$error_t[2]."<br> DELETE FROM $table WHERE {$this->where}");
		}

		return $this;
		
	}

	function affected_rows()
	{
		return $this->affected_rows;
	}

	function insert_id()
	{
		return $this->pdo->lastInsertId();
	}

	function optimaze_table($table)
	{
		$this->query("OPTIMIZE TABLE $table ");
		return $this;
	}

	function truncate_table($table)
	{
		
		$this->affected_rows = $this->pdo->exec("TRUNCATE TABLE $table ");

		$error_t = $this->pdo->errorInfo();
		if($error_t[1] != '')
		{
			exit('Error No: '.$error_t[1].'<br>Error Co: '.$error_t[2]."<br> TRUNCATE TABLE $table ");
		}

		return $this;
	}

	function drop_table($table)
	{
		$this->query("DROP TABLE $table ");
		return $this;
	}

	function __destruct()
	{
		$this->pdo = null;
	}
}

?>
