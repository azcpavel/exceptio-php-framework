<?php
/**
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	DB Class
*/

Final class DbClass
{	
	private $select 	= NULL;	
	private $table;
	private $join 		= '';
	private $where 		= 1;
	private $order_by 	= '';
	private $group_by 	= '';
	private $limit 		= '';
	private $query;
	private $query_str;
	private $wrapColumnStart = '';
	private $wrapColumnEnd = '';
	private $prepare;
	private $prepareSql;
	private $affected_rows;
	private $db_driver;
	private $select_table_prefix = '';
	
	// Public Property
	public $db_host;
	public $db_uname;
	public $db_pass;
	public $db_name;
	public $db_prefix;
	public $db_service;
	public $db_protocol;
	public $db_server;
	public $db_uid;
	public $db_options;
	public $errors = '';

	
	function __construct($driver = '',$host = '',$user = '',$pass = '',$db = '',$dbPrefix = '',$port = '',$service = '',$protocol = '',$server = '',$uid = '',$options = '', $autocommit = true, $preExecute = '')
	{		
		
		require_once(SYSTEM.'/class/dbDriver/'.$driver.'.inc');

		try{			
			//look($dsn);
			$this->db_driver 	= $driver;
			$this->db_host 		= $host;
			$this->db_uname 	= $user;
			$this->db_pass 		= $pass;
			$this->db_name 		= $db;
			$this->db_prefix 	= $dbPrefix;
			$this->db_host 		= $host;
			$this->db_port 		= $port;
			$this->db_service 	= $service;
			$this->db_protocol 	= $protocol;
			$this->db_server 	= $server;
			$this->db_uid 		= $uid;
			$this->db_options	= $options;

			if(is_array($options) && count($options) > 0)
				$this->pdo = @new pdo($dsn,$user,$pass,$this->db_options);
			else
				$this->pdo = @new pdo($dsn,$user,$pass);
			
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			if($autocommit)
				$this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
			else if($driver != 'dblib' || substr(phpversion(), 0,1) == 7)				
				$this->pdo->setAttribute(PDO::ATTR_PERSISTENT, true);			

			if(is_array($preExecute) && count($preExecute) > 0)
			foreach ($preExecute as $value) {
				$this->pdo->exec($value);
			}

		} catch (PDOException $error) {
		    $this->printError($error);
		}
		
	}

	function __call($mth_name,$mth_arg)
	{
		echo "Unknown Member Call $mth_name<br>You can get all details by calling get_class_details() method<br>".PHP_EOL;
	}

	function __get($porp_name){
		echo "Unknown Property Call $porp_name<br>You can get all details by calling get_class_details() method<br>".PHP_EOL;	
	}

	private function printError($error)
	{
		if(SHOW_DB_ERROR == false){
			$this->errors = array('message' => $error->getMessage(), 'trace' => $error->getTrace());			
			return $this;			
		}
		
		echo "Query Error:<br><br>".$error->getMessage()."<br><br>";
		echo "Query Trace:<br><br>";
		foreach ($error->getTrace() as $key => $value) {
			echo 'In file "'.$value['file'].'"<br>';
			echo 'In line "'.$value['line'].'"<br>';
			echo 'In function "'.$value['function'].'"<br>';
			echo (isset($value['class'])) ? 'In class "'.$value['class'].'"<br>' : '';
			echo (isset($value['type'])) ? 'In type "'.$value['type'].'"<br>' : '';
			foreach ($value['args'] as $argsKey => $argsValue) {
				if(is_array($argsValue))
					foreach ($argsValue as $argsKeySub => $argsValueSub) {
						echo (@strlen($argsValueSub) > 0) ? 'In args "'.$argsKeySub." => ".$argsValueSub.'"<br>' : '';	
					}
				else
					echo (strlen($argsValue) > 0) ? 'In args "'.$argsValue.'"<br>' : '';	
			}
			echo "<br>";
		}
		die();
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

	function begin_transaction(){
		$this->pdo->beginTransaction();
	}

	function roll_back(){
		$this->pdo->rollBack();
	}

	function commit(){
		$this->pdo->commit();
	}
	
	function prepare($sql = ''){
		try{
			$this->query = $this->pdo->prepare($sql);
		}
		catch(PDOException $error){
			$this->printError($error);
		}
		return $this;
	}
	
	function bind_value($index, $value, $data_type){
		try{
			$this->query->bindValue($index, $value, $data_type);
		}
		catch(PDOException $error){
			$this->printError($error);
		}
		return $this;
	}
	
	function execute($query = NULL)
	{
		try{
			if($query)
				$this->query->execute($query);
			else
				$this->query->execute();
		}
		catch (PDOException $error)
		{
			$this->printError($error);
		}
	
		return $this;
	}
	
	function exec($query = NULL)
	{
		try{
			if($query)
				$this->affected_rows = $this->pdo->exec($query);			
		}
		catch (PDOException $error)
		{
			$this->printError($error);
		}
	
		return $this;
	}
	

	function query($query = "")
	{
		$select = ($this->select == NULL ) ? '*' : $this->select;
		if ($query === "")
			$query = "SELECT {$select} FROM {$this->db_prefix}{$this->table} {$this->join} ".( ($this->where !== 1) ? "WHERE ".$this->where : '')." {$this->group_by} {$this->order_by} {$this->limit}";

		$this->query_str = $query;
		try{			
			$this->query = $this->pdo->query($query) or $error_t = $this->pdo->errorInfo();			
		}
		catch(PDOException $error){
			$this->printError($error);
		}		
		
		$this->order_by = $this->group_by = $this->join = $this->limit = $select_table_prefix = '';
		$this->where = 1;
		$this->select = NULL;
		
		return $this;
	}

	function get($table = 0)
	{
		$this->table = (($table !== 0) ? $table : $this->table);
		
		if(count(func_get_args()) > 1 )
			$this->limit = "LIMIT ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(1));
		if(count(func_get_args()) > 2 )
			$this->limit = "LIMIT ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(1).", ".func_get_arg(2))."";		

		$this->query();
		
		return $this;
	}

	function get_where($table = 0, $where = 1)
	{
		$this->table = (($table !== 0) ? $table : $this->table);

		if(count(func_get_args()) > 2 )
			$this->limit = "LIMIT ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(2));
		if(count(func_get_args()) > 3 )
			$this->limit = "LIMIT ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(2)).", ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(3))."";		

		$this->where($where);

		$this->query();

		return $this;
	}

	function select($select = NULL)
	{
		if(is_array($select)){
			
			if(isset($select['db_table']))
			{							
				$this->select_table_prefix = $this->db_prefix.$select['db_table'].'.';
				unset($select['db_table']);
			}
			foreach ($select as $key => $value) {
				if($this->select != NULL)
					$this->select .= ',';				
				
				if(preg_match('/\(/', $value)){
										
					$this->select .= preg_replace_callback('/\(\w+\)/', array($this,'selectReplaceCallBack'), $value);
				}
				else
					$this->select .= $this->select_table_prefix.$value;
			}
		}else{

			if($this->select != NULL)
				$this->select .= ',';
			$this->select .= $select;
		}

		$this->select_table_prefix = '';
		return $this;
	}

	private function selectReplaceCallBack($match){

		return preg_replace('/\(/', '('.$this->select_table_prefix, $match[0]);
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

	function join($table, $join, $pos = 0, $preFix = 1){
		if (strpos($table, ' ') === false && $preFix == 1) {		
			$join = explode('=', $join);
			foreach ($join as $key => $value) {
				$join[$key] = $this->db_prefix.trim($value);
			}
			$join = implode('=', $join);
		}

		$this->join .=  (($pos !== 0) ? " ".strtoupper($pos)." JOIN {$this->db_prefix}$table ON $join " : "JOIN {$this->db_prefix}$table ON $join ");

		return $this;
	}

	function group_by($group_by = '')
	{
		$this->group_by = 'GROUP BY '.$group_by;

		return $this;
	}

	function where($where = 1, $isSec = false)
	{
		$where_full = '';		
		$db_table	= '';
		if(is_array($where)){
			if(isset($where['db_table']))
			{							
				$db_table 	= $this->db_prefix.$where['db_table'];
				unset($where['db_table']);
			}
			foreach ($where as $key => $value) {				

				if($this->db_driver != 'dblib'){

					if($db_table == '')
						$db_table = $this->wrapColumnStart;
					else
						$db_table = $this->wrapColumnStart.$db_table.'.';

					if( preg_match('/<|>|\=|!| LIKE| BETWEEN/', $key) && (preg_match('/ AND$| OR$|\'|^$/', $value)) )
						$where_full .= " {$db_table}$key{$this->wrapColumnEnd} $value";
					else if( preg_match('/ LIKE| BETWEEN/', $key) )
						$where_full .= " {$db_table}$key{$this->wrapColumnEnd} $value AND";
					elseif(preg_match('/IN| NOT IN/', $key))
						$where_full .= " {$db_table}$key{$this->wrapColumnEnd} ($value)";
					elseif(preg_match('/<|>|\=|!/', $key))
						$where_full .= " {$db_table}$key{$this->wrapColumnEnd} '$value' AND";
					elseif (preg_match('/ AND$| OR$|^\(|\)$/', $value))
						$where_full .= " {$db_table}$key{$this->wrapColumnEnd} = $value";
					else
						$where_full .= " {$db_table}$key{$this->wrapColumnEnd} = '$value' AND";
				}else{
					
					if($db_table == '')
						$db_table = $this->wrapColumnStart;
					else
						$db_table = $this->wrapColumnStart.$db_table.$this->wrapColumnEnd.'.'.$this->wrapColumnStart.'dbo'.$this->wrapColumnEnd.'.'.$this->wrapColumnStart;					

					if( preg_match('/<|>|\=|!| LIKE| BETWEEN/', $key) && (preg_match('/ AND$| OR$|\'|^$/', $value)) )
						$where_full .= " {$db_table}$key{$this->wrapColumnEnd} $value";
					else if( preg_match('/ LIKE| BETWEEN/', $key) )
						$where_full .= " {$db_table}$key{$this->wrapColumnEnd} $value AND";
					elseif(preg_match('/IN| NOT IN/', $key))
						$where_full .= " {$db_table}$key{$this->wrapColumnEnd} ($value)";
					elseif(preg_match('/<|>|\=|!/', $key))
						$where_full .= " {$db_table}$key{$this->wrapColumnEnd} '$value' AND";
					elseif (preg_match('/ AND$| OR$|^\(|\)$/', $value))
						$where_full .= " {$db_table}$key{$this->wrapColumnEnd} = $value";
					else
						$where_full .= " {$db_table}$key{$this->wrapColumnEnd} = '$value' AND";
				}
			}

			
			if(substr($where_full,-4) === ' AND'){				
				if($this->where != 1)
					$this->where .= ' AND'.substr($where_full, 0, -4);
				else
					$this->where = substr($where_full, 0, -4);
			}
			else{
				if($this->where != 1)
					$this->where .= ' AND '.$where_full;
				else
					$this->where = $where_full;
			}
			
			
		}
		elseif($this->where != 1 AND $isSec != false)
			$this->where .= " AND $where = '$isSec'";		
		elseif($this->where != 1 AND $isSec == false)
			$this->where .= " AND $where";
		elseif($this->where == 1 AND $isSec != false)
			$this->where = "$where = '$isSec'";
		else
			$this->where = $where;

		return $this;
	}

	function order_by($order_by = NULL, $order = NULL)
	{
		
		if($this->order_by == ''){
			if($order_by && $order == NULL)
				$this->order_by = "ORDER BY $order_by";
			else
				$this->order_by = "ORDER BY $order_by $order";
		}
		else{
			if($order_by && $order == NULL)
				$this->order_by .= ", $order_by";
			else
				$this->order_by .= ", $order_by $order";
		}			

		return $this;
	}

	function limit()
	{
		if(count(func_get_args()) > 1 )
			$this->limit = "LIMIT ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(0)).", ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(1))."";
		else
			$this->limit = "LIMIT ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(0));

		return $this;
	}

	function num_rows()
	{
		$num = NULL;
		try{
			$num = $this->pdo->query($this->query_str);
		}		
		catch (PDOException $error)
		{
			$this->printError($error);
		}
		if($this->errors == '')		
			$num = count($num->fetchAll());
		return $num;
	}
	
	function next_rowset(){
		if($this->query)
			return $this->query->nextRowset();
		return $this;		
	}

	function row_array($index = 0, $type = 1)
	{
		if($this->errors == ''){
			$return = $this->result_array($type);
			if(isset($return[$index]))
				return $return[$index];
			else
				return false;
		}
		return $this;
	}

	function result_array($type = 1)
	{
		if($this->errors == ''){
			if($type)
				return $this->query->fetchAll(PDO::FETCH_ASSOC);
			else
				return $this->query->fetchAll();
		}
		return $this;
	}

	function row($index = 0)
	{		
		if($this->errors == ''){
			$return = $this->result();
			if(isset($return[$index]))
				return $return[$index];
			else
				return false;
		}

		return $this;
	}

	function result()
	{
		if($this->errors == '')
			return $this->query->fetchAll(PDO::FETCH_CLASS);
		return $this;
	}

	function fetch_column($limit = 0)
	{
		return $this->query->fetchColumn($limit);
	}

	function show_column($table)
	{
		return $this->query("SHOW COLUMNS FROM {$this->db_prefix}$table");
	}

	function show_tables()
	{
		return $this->query("SHOW TABLES FROM {$this->db_name}");
	}


	function insert($table, $values = 1, $typeQr = 'INSERT')
	{

		$values_full = '(';
		$key_full = '';
		
		if(is_array($values)){
			$key_full = '(';
			foreach ($values as $key => $value) {
				
					$values_full .= "'$value',";
					$key_full .= "{$this->wrapColumnStart}$key{$this->wrapColumnEnd},";
			}

			$values_full = substr($values_full, 0, -1).')';
			$key_full = substr($key_full, 0, -1).')';
		}
		else
			$values_full .= $values.')';
	
		try{
			$this->affected_rows = $this->pdo->exec("{$typeQr} INTO {$this->db_prefix}{$table} $key_full VALUES {$values_full}"); 	
		}
		catch (PDOException $error)
		{
			$this->printError($error);
		}		

		return $this;

	}
	
	function insert_multi($table, $keys , $values = 1, $typeQr = 'INSERT')
	{
	
		$values_full = '';
		$key_full = '(';
	
		if(is_array($keys)){
			$key_full = '(';
			foreach ($keys as $keyKeys => $valueKeys) {				
				$key_full .= "{$this->wrapColumnStart}$valueKeys{$this->wrapColumnEnd},";
			}			
			$key_full = substr($key_full, 0, -1).')';
		}
		else
			$key_full .= $keys.')';
		
		if(is_array($values)){			
			foreach ($values as $keyValues => $valueValues) {
				$values_full .= '(';
				foreach ($valueValues as $value){					
					$values_full .= "'$value',";					
				}
				$values_full = substr($values_full, 0, -1).'),';
			}
		
			$values_full = substr($values_full, 0, -1);
			
		}
		else
			$values_full .= $values;
	
		try{
			$this->affected_rows = $this->pdo->exec("{$typeQr} INTO {$this->db_prefix}{$table} $key_full VALUES {$values_full}");
		}
		catch (PDOException $error)
		{
			$this->printError($error);
		}
	
		return $this;
	
	}

	function update($table, $values = '', $where = false)
	{		
		$values_full = '';

		if($where != false)
			$this->where($where);

		if(is_array($values)){
			foreach ($values as $key => $value) {
				
					$values_full .= " {$this->wrapColumnStart}$key{$this->wrapColumnEnd} = '$value',";
			}

			$values_full = substr($values_full, 0, -1);
		}
		else
			$values_full = $values;

		try{
			$this->affected_rows = $this->pdo->exec("UPDATE {$this->db_prefix}$table SET$values_full WHERE {$this->where} ");
		}		
		catch (PDOException $error)
		{
			$this->printError($error);
		}

		$this->where = 1;

		return $this;
	}


	function delete($table, $where = 1)
	{		

		$this->where($where);

		try{
			$this->affected_rows = $this->pdo->exec("DELETE FROM {$this->db_prefix}$table WHERE {$this->where}");
		}
		catch (PDOException $error)
		{
			$this->printError($error);
		}

		$this->where = 1;
		
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

	function last_row($parm = 'obj')
	{
		$last = ($parm != 'obj') ? $this->result_array() : $this->result();
		if (count($last) > 0) {			
			return $last[count($last)-1];
		}
	}

	function optimaze_table($table)
	{
		$this->query("OPTIMIZE TABLE {$this->db_prefix}$table ");
		return $this;
	}

	function truncate_table($table)
	{
		try{
			$this->affected_rows = $this->pdo->exec("TRUNCATE TABLE {$this->db_prefix}$table ");
		}
		catch (PDOException $error)
		{
			$this->printError($error);
		}

		return $this;
	}

	function drop_table($table)
	{
		$this->query("DROP TABLE $table ");
		return $this;
	}

	function get_errors(){
		return $this->errors;
	}


	function __destruct()
	{		
		$this->pdo = null;
	}
}

?>
