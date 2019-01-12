<?php
require_once(SYSTEM.'/systemconfig.php');
require_once(SYSTEM.'/class/dbclass.php');

class ExceptioCommand {
	private $argv;
	private $migrationDir;
	public function __construct($argv){
		$this->argv = $argv;
		$this->migrationDir = APPLICATION.'/database/migrations/';
	}

	private function assetVersion(){
		$handle = fopen(SYSTEM.'/assetVersion.php','w');
		if($handle){
			$time = time();
			fwrite($handle, '<?php'.PHP_EOL.
							'define(\'ASSET_VERSION\', '.$time.');'.PHP_EOL.
						'?>');
			fclose($handle);

			echo "Success new version is: ".$time;
		}
	}

	private function doMigration($name = 'default'){
		require(APPLICATION.'/config/database.php');

		$db = new dbClass($db_config[$name]['driver'],$db_config[$name]['host'],$db_config[$name]['user'],
				$db_config[$name]['pass'],$db_config[$name]['db'],$db_config[$name]['dbPrefix'],$db_config[$name]['port'],
				$db_config[$name]['service'],$db_config[$name]['protocol'],$db_config[$name]['server'],
				$db_config[$name]['uid'],$db_config[$name]['options'],$db_config[$name]['autocommit'],
				$db_config[$name]['preExecute'],$db_config[$name]['useDbEscape'],$db_config[$name]['charset'],
				$db_config[$name]['collation'],$db_config[$name]['engine']);

		$checkTable = $db->query("SHOW tables like '{$db_config[$name]['dbPrefix']}migrations'")->num_rows();
		if($checkTable < 1){
			echo "Creating migration table for database:".$db_config[$name]['db'].PHP_EOL;
			$db->create_table(
								'migrations',
								array(
									'id' => array(
										'type' 		=> 'int(10)',
										'option' 	=> 'UNIQUE AUTO_INCREMENT'
									),
									'migration' => array(
										'type' 		=> 'varchar(191)',
										'option'	=> 'NOT NULL'
									),
									'batch' => array(
										'type' 		=> 'int(11)',
										'option'	=> 'NOT NULL'
									),
								),
								true
							);						
		}		
		
		$maxBatch = $db->select('ifNull(max(batch),0) batch')->get('migrations')->row()->batch;
		$maxBatch++;

		$files = new DirectoryIterator($this->migrationDir);
		$fileNames = [];
		foreach ($files as $key => $fileInfo) {
			if($fileInfo->isDot() || $fileInfo->getExtension() != 'php') continue;

			$fileName = explode('_', $fileInfo->getBasename('.php'));
			$fileNames[$fileName[0]] = $fileName[1];
		}

		ksort($fileNames);
		
		foreach ($fileNames as $key => $value) {
			$file = $key.'_'.$value;
			$code = $key;
			$name = $value;		
			$chk = $db->get_where('migrations',array('migration' => $file))->num_rows();
			if($chk < 1){
				echo "migrating ".$file.PHP_EOL;
				require_once($this->migrationDir.$file.'.php');
				$className = $name.$code;
				$sqlCommands = new $className;				
				foreach ($sqlCommands->up() as $keySQL => $valueSQL) {
					if($valueSQL != "")
						$db->exec($valueSQL);
				}
				$db->insert('migrations',[
					'migration' => $file,
					'batch'		=> $maxBatch
				]);
				echo "done ".$file.PHP_EOL;
			}
		}
	}

	private function doMigrationAll(){
		require(APPLICATION.'/config/database.php');
		foreach ($db_config as $key => $value) {
			$this->doMigration($key);
		}
	}

	private function addMigration(){
		$rand = date('YmdHis').rand(1000,9999);
		$fileName = $rand.'_'.$this->argv[3];

		$handle = fopen(APPLICATION.'/database/migrations/'.$fileName.'.php','w');
		if($handle){		
			fwrite($handle, '<?php'.PHP_EOL.
							'class '.$this->argv[3].$rand.'{'.PHP_EOL.
							'	/**'.PHP_EOL.
						    '	* Run the migrations.'.PHP_EOL.
						    '	*'.PHP_EOL.
						    '	* @return sql command in array'.PHP_EOL.
						    '	*/'.PHP_EOL.
						    '	'.PHP_EOL.
						    '	public function up(){'.PHP_EOL.
						    '		//your sql command array'.PHP_EOL.
						    '		return array();'.PHP_EOL.
	    					'	}'.PHP_EOL.
	    					'	/**'.PHP_EOL.
						    '	* Reverse the migrations.'.PHP_EOL.
						    '	*'.PHP_EOL.
						    '	* @return sql command in array'.PHP_EOL.
						    '	*/'.PHP_EOL.
						    '	'.PHP_EOL.
						    '	public function down(){'.PHP_EOL.
						    '		//your sql command array'.PHP_EOL.
						    '		return array();'.PHP_EOL.
	    					'	}'.PHP_EOL.
							'}'.PHP_EOL.
						'?>');
			fclose($handle);

			echo "Success : ".$fileName;
		}
	}

	private function rollBackMigration($step = false, $name = 'default'){
		require(APPLICATION.'/config/database.php');

		$db = new dbClass($db_config[$name]['driver'],$db_config[$name]['host'],$db_config[$name]['user'],
				$db_config[$name]['pass'],$db_config[$name]['db'],$db_config[$name]['dbPrefix'],$db_config[$name]['port'],
				$db_config[$name]['service'],$db_config[$name]['protocol'],$db_config[$name]['server'],
				$db_config[$name]['uid'],$db_config[$name]['options'],$db_config[$name]['autocommit'],
				$db_config[$name]['preExecute'],$db_config[$name]['useDbEscape'],$db_config[$name]['charset'],
				$db_config[$name]['collation'],$db_config[$name]['engine']);

		$checkTable = $db->query("SHOW tables like '{$db_config[$name]['dbPrefix']}migrations'")->num_rows();
		if($checkTable > 0){
			$migrations = $db->order_by('id','DESC')->get('migrations');			
			$stepCount = 1;
			if($migrations->num_rows() > 0)
			foreach ($migrations->result() as $keyStep => $valueStep) {				
				if($step && $stepCount > $step)
					break;
				
				$file = $valueStep->migration;
				$code = explode('_', $file)[0];
				$name = explode('_', $file)[1];
				echo "rolling back ".$file.PHP_EOL;
				require_once($this->migrationDir.$file.'.php');
				$className = $name.$code;
				$sqlCommands = new $className;				
				foreach ($sqlCommands->down() as $keySQL => $valueSQL) {
					if($valueSQL != "")
						$db->exec($valueSQL);
				}
				$db->delete('migrations',array('id' => $valueStep->id));					
				echo "done ".$file.PHP_EOL;					
				

				$stepCount++;
			}
		}else{
			echo "No migration table found in database:".$db_config[$name]['db'].PHP_EOL;
			echo "run 'php exceptio migrate'".PHP_EOL;
		}
	}

	private function rollBackMigrationAll($step = false){
		require(APPLICATION.'/config/database.php');
		foreach ($db_config as $key => $value) {
			$this->rollBackMigration($step, $key);
		}
	}

	private function refreshMigration($name = 'default'){
		$this->rollBackMigration(false, $name);
		$this->doMigration($name);
	}

	private function refreshMigrationAll(){
		require(APPLICATION.'/config/database.php');
		foreach ($db_config as $key => $value) {
			$this->refreshMigration($key);
		}
	}

	public function run(){
		if(isset($this->argv[1]) 
			&& $this->argv[1] == 'assetVersion'){

			$this->assetVersion();

		}else if(isset($this->argv[1]) 
			&& $this->argv[1] == 'migration'
			&& isset($this->argv[2]) 
			&& strpos($this->argv[2], 'add') !== false){

			$this->addMigration();

		}else if(isset($this->argv[1]) 
			&& $this->argv[1] == 'migration'
			&& isset($this->argv[2]) 
			&& strpos($this->argv[2], 'rollbackdb') !== false){
			$allDB = strpos($this->argv[2], ':all') !== false;
			$oneDB = strpos($this->argv[2], '=') !== false;
			$step = ((isset($this->argv[3]) && strpos($this->argv[3], 'step') !== false) ? explode('=', $this->argv[3])[1] : false);
			if($allDB){
				$this->rollBackMigrationAll($step);
			}else if($oneDB){
				$this->rollBackMigration($step, explode('=', $argv[2])[1]);
			}else{
				$this->rollBackMigration($step);
			}	

		}else if(isset($this->argv[1]) 
			&& $this->argv[1] == 'migrate' 
			&& isset($this->argv[2])
			&& strpos($this->argv[2], 'refreshdb') !== false){
			
			$allDB = strpos($this->argv[2], ':all') !== false;
			$oneDB = strpos($this->argv[2], '=') !== false;
			if($allDB){
				$this->refreshMigrationAll();
			}else if($oneDB){
				$this->refreshMigration(explode('=', $argv[2])[1]);
			}else{
				$this->refreshMigration();
			}

		}else if(isset($this->argv[1]) 
			&& $this->argv[1] == 'migrate' 
			&& isset($this->argv[2]) 
			&& strpos($this->argv[2], 'db') !== false){
			
			$allDB = strpos($this->argv[2], ':all') !== false;
			$oneDB = strpos($this->argv[2], '=') !== false;
			if($allDB){
				$this->doMigrationAll();
			}else if($oneDB){
				$this->doMigration(explode('=', $argv[2])[1]);
			}else{
				$this->doMigration();
			}

		}else if(isset($this->argv[1]) 
			&& $this->argv[1] == 'migrate'){

			$this->doMigration();

		}
	}

}

?>