<?php
require_once(SYSTEM.'/systemconfig.php');
require_once(SYSTEM.'/class/dbclass.php');

class ExceptioCommand {
	private $argv;
	private $migrationDir;
	private $foreground_colors = array();
	private $background_colors = array();

	public function __construct($argv){
		$this->argv = $argv;
		$this->migrationDir = APPLICATION.'/database/migrations/';

		$this->setSellColor();
	}

	private function setSellColor(){
		// Set up shell colors
		$this->foreground_colors['black'] = '0;30';
		$this->foreground_colors['dark_gray'] = '1;30';
		$this->foreground_colors['blue'] = '0;34';
		$this->foreground_colors['light_blue'] = '1;34';
		$this->foreground_colors['green'] = '0;32';
		$this->foreground_colors['light_green'] = '1;32';
		$this->foreground_colors['cyan'] = '0;36';
		$this->foreground_colors['light_cyan'] = '1;36';
		$this->foreground_colors['red'] = '0;31';
		$this->foreground_colors['light_red'] = '1;31';
		$this->foreground_colors['purple'] = '0;35';
		$this->foreground_colors['light_purple'] = '1;35';
		$this->foreground_colors['brown'] = '0;33';
		$this->foreground_colors['yellow'] = '1;33';
		$this->foreground_colors['light_gray'] = '0;37';
		$this->foreground_colors['white'] = '1;37';

		$this->background_colors['black'] = '40';
		$this->background_colors['red'] = '41';
		$this->background_colors['green'] = '42';
		$this->background_colors['yellow'] = '43';
		$this->background_colors['blue'] = '44';
		$this->background_colors['magenta'] = '45';
		$this->background_colors['cyan'] = '46';
		$this->background_colors['light_gray'] = '47';
	}

	// Returns colored string
	public function getColoredString($string, $foreground_color = null, $background_color = null) {
		$colored_string = "";

		// Check if given foreground color found
		if (isset($this->foreground_colors[$foreground_color])) {
			$colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
		}
		// Check if given background color found
		if (isset($this->background_colors[$background_color])) {
			$colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
		}

		// Add string and end coloring
		$colored_string .=  $string . "\033[0m";

		return $colored_string;
	}

	// Returns all foreground color names
	public function getForegroundColors() {
		return array_keys($this->foreground_colors);
	}

	// Returns all background color names
	public function getBackgroundColors() {
		return array_keys($this->background_colors);
	}

	private function assetVersion(){
		$handle = fopen(SYSTEM.'/assetVersion.php','w');
		if($handle){
			$time = time();
			fwrite($handle, '<?php'.PHP_EOL.
							'define(\'ASSET_VERSION\', '.$time.');'.PHP_EOL.
						'?>');
			fclose($handle);

			echo $this->getColoredString("Success new version is: ","green").$time;
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
			echo $this->getColoredString("Creating migration table for database:".$db_config[$name]['db'].PHP_EOL,"yellow");
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
				echo $this->getColoredString("migrating ","yellow").$file.PHP_EOL;
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
				echo $this->getColoredString("done ","green").$file.PHP_EOL;
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

			echo $this->getColoredString("Success : ","green").$fileName;
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
				echo $this->getColoredString("rolling back ","yellow").$file.PHP_EOL;
				require_once($this->migrationDir.$file.'.php');
				$className = $name.$code;
				$sqlCommands = new $className;				
				foreach ($sqlCommands->down() as $keySQL => $valueSQL) {
					if($valueSQL != "")
						$db->exec($valueSQL);
				}
				$db->delete('migrations',array('id' => $valueStep->id));					
				echo $this->getColoredString("done ","green").$file.PHP_EOL;
				

				$stepCount++;
			}
		}else{
			echo $this->getColoredString("No migration table found in database:".$db_config[$name]['db'].PHP_EOL,"red");
			echo $this->getColoredString("run 'php exceptio migrate'".PHP_EOL,"red");
		}
	}

	private function rollBackMigrationAll($step = false){
		require(APPLICATION.'/config/database.php');
		foreach ($db_config as $key => $value) {
			$this->rollBackMigration($step, $key);
		}
	}

	private function refreshMigration($step = false, $name = 'default'){
		$this->rollBackMigration($step, $name);
		$this->doMigration($name);
	}

	private function refreshMigrationAll($step = false){
		require(APPLICATION.'/config/database.php');
		foreach ($db_config as $key => $value) {
			$this->refreshMigration($step, $key);
		}
	}

	private function showMigration($name = 'default'){
		require(APPLICATION.'/config/database.php');

		$db = new dbClass($db_config[$name]['driver'],$db_config[$name]['host'],$db_config[$name]['user'],
				$db_config[$name]['pass'],$db_config[$name]['db'],$db_config[$name]['dbPrefix'],$db_config[$name]['port'],
				$db_config[$name]['service'],$db_config[$name]['protocol'],$db_config[$name]['server'],
				$db_config[$name]['uid'],$db_config[$name]['options'],$db_config[$name]['autocommit'],
				$db_config[$name]['preExecute'],$db_config[$name]['useDbEscape'],$db_config[$name]['charset'],
				$db_config[$name]['collation'],$db_config[$name]['engine']);

		$checkTable = $db->query("SHOW tables like '{$db_config[$name]['dbPrefix']}migrations'")->num_rows();
		if($checkTable > 0){

			$maxLen = 0;
			$files = new DirectoryIterator($this->migrationDir);
			$fileNames = [];
			foreach ($files as $key => $fileInfo) {
				if($fileInfo->isDot() || $fileInfo->getExtension() != 'php') continue;
				$len = strlen($fileInfo->getBasename('.php'));
				$maxLen = $len > $maxLen ? $len : $maxLen;
				$fileName = explode('_', $fileInfo->getBasename('.php'));
				$fileNames[$fileName[0]] = $fileName[1];
			}

			ksort($fileNames);
			$migrations = $db->order_by('id','DESC')->get('migrations')->result();

			echo "+-------+-".str_pad("", $maxLen, '-')."-+-------+".PHP_EOL;
			echo "| ".$this->getColoredString("Done?","green")." | ".$this->getColoredString("Migration","green").$this->getColoredString("[{$db_config[$name]['db']}]","yellow").str_pad("", $maxLen - strlen("Migration[{$db_config[$name]['db']}]"))." | ".$this->getColoredString("Batch","green")." |".PHP_EOL;
			echo "+-------+-".str_pad("", $maxLen, '-')."-+-------+".PHP_EOL;
			foreach ($fileNames as $keyF => $valueF) {
				$found = false;
				$batch = "";
				foreach ($migrations as $keyM => $valueM) {
					if($valueM->migration == $keyF.'_'.$valueF){
						$found = true;
						$batch = $valueM->batch;
						unset($migrations[$keyM]);
						break;
					}
				}

				if($found)
					echo "| ".$this->getColoredString("Yes","green").str_pad("", 5 - strlen("Yes"))." | ".str_pad($keyF.'_'.$valueF, $maxLen)." | ".$batch.str_pad("", 5 - strlen($batch))." |".PHP_EOL;
				else
					echo "| ".$this->getColoredString("No","red").str_pad("", 6 - strlen("Yes"))." | ".str_pad($keyF.'_'.$valueF, $maxLen)." | ".$batch.str_pad("", 5 - strlen($batch))." |".PHP_EOL;
			}

			echo "+-------+-".str_pad("", $maxLen, '-')."-+-------+".PHP_EOL;
		}else{
			echo $this->getColoredString("No migration table found in database:".$db_config[$name]['db'].PHP_EOL,"red");
			echo $this->getColoredString("run 'php exceptio migrate'".PHP_EOL,"red");
		}
	}

	private function showMigrationAll($name = 'default'){
		require(APPLICATION.'/config/database.php');
		foreach ($db_config as $key => $value) {
			$this->showMigration($key);
		}
	}

	public function run(){
		if(isset($this->argv[1]) 
			&& $this->argv[1] == 'assetVersion'){

			$this->assetVersion();

		}else if(isset($this->argv[1]) 
			&& $this->argv[1] == 'migrate'
			&& isset($this->argv[2]) 
			&& strpos($this->argv[2], 'add') !== false){

			$this->addMigration();

		}else if(isset($this->argv[1]) 
			&& $this->argv[1] == 'migrate'
			&& isset($this->argv[2]) 
			&& strpos($this->argv[2], 'rollbackdb') !== false){
			$allDB = strpos($this->argv[2], ':all') !== false;
			$oneDB = strpos($this->argv[2], '=') !== false;
			$step = ((isset($this->argv[3]) && strpos($this->argv[3], 'step') !== false) ? explode('=', $this->argv[3])[1] : false);
			if($allDB){
				$this->rollBackMigrationAll($step);
			}else if($oneDB){
				$this->rollBackMigration($step, explode('=', $this->argv[2])[1]);
			}else{
				$this->rollBackMigration($step);
			}	

		}else if(isset($this->argv[1]) 
			&& $this->argv[1] == 'migrate' 
			&& isset($this->argv[2])
			&& strpos($this->argv[2], 'refreshdb') !== false){
			
			$allDB = strpos($this->argv[2], ':all') !== false;
			$oneDB = strpos($this->argv[2], '=') !== false;
			$step = ((isset($this->argv[3]) && strpos($this->argv[3], 'step') !== false) ? explode('=', $this->argv[3])[1] : false);
			if($allDB){
				$this->refreshMigrationAll($step);
			}else if($oneDB){
				$this->refreshMigration($step, explode('=', $this->argv[2])[1]);
			}else{
				$this->refreshMigration($step);
			}

		}else if(isset($this->argv[1]) 
			&& $this->argv[1] == 'migrate'
			&& isset($this->argv[2]) 
			&& strpos($this->argv[2], 'statusdb') !== false){

			$allDB = strpos($this->argv[2], ':all') !== false;
			$oneDB = strpos($this->argv[2], '=') !== false;
			if($allDB){
				$this->showMigrationAll();
			}else if($oneDB){
				$this->showMigration(explode('=', $this->argv[2])[1]);
			}else{
				$this->showMigration();
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
				$this->doMigration(explode('=', $this->argv[2])[1]);
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