<?php

/**
 * 
 */
class dbmanager {

	private $dir 		= '';
	private $name 		= '';
	private $db_host 	= '';
	private $db_uname 	= '';
	private $db_pass 	= '';
	private $db_name 	= '';
	private $filename 	= 'backup';
	private $tables 	= NULL;

    function __construct($argument = NULL) {
        # code...
    }

    /**
     * @author awan < http://www.nawa.me >
     * @edited By Ahsan Zahid Chowdhury < http://itszahid.info >
     * @Description To enable custome tables selection during export and fixed import error
     */

    /**
     * Function to build SQL /Exporting SQL DATA
     *
     * @param string directory , as the directory to put file
     * @param string $dbhost database host
     * @param string $dbuser database user
     * @param string $dbpass database password
     * @param string $dbname database name
     * @param array $tables name in array default NULL
     * @return string complete if complete
     */

    function exportMysqlDb($config =  array( 
				    						'dir' 	=> '',
				    						'name' 	=> '',
				    						'db_host' => '',
				    						'db_uname' => '',
				    						'db_pass' => '',
				    						'db_name' => '',
				    						'filename' => '',
				    						'tables' => NULL)
				    						) {

    	foreach ($config as $key => $value) {
    		$this->$key = $value; // Assign the values to valiables
    	}

    	if($this->dir == '')
    		$this->dir = dirname(__FILE__);    	

        return $this->backup_MysqliDatabase($this->dir, $this->filename, $this->db_host, $this->db_uname, $this->db_pass, $this->db_name, $this->tables); // execute		
    }

    /**
     * Function to build SQL /Exporting SQL DATA
     *
     * @param string directory , as the directory to put file
     * @param string $dbhost database host
     * @param string $dbuser database user
     * @param string $dbpass database password
     * @param string $dbname database name
     * @param array $tables name in array default NULL
     * @return string complete if complete
     */

    function importMysqlDb($config =  array( 
				    						'dir' 	=> '',
				    						'name' 	=> '',
				    						'db_host' => '',
				    						'db_uname' => '',
				    						'db_pass' => '',
				    						'db_name' => '',
				    						'filename' => '',
				    						'tables' => NULL)
				    						) {

    	foreach ($config as $key => $value) {
    		$this->$key = $value; // Assign the values to valiables
    	}

    	if($this->dir == '')
    		$this->dir = dirname(__FILE__);

    	$file = $this->dir.$this->filename; // sql data file
		$args = file_get_contents($file); // get contents 

        return $this->import_MysqliDatabase($args, $this->db_host, $this->db_uname, $this->db_pass, $this->db_name); // execute		
    }


    /**
     * Function to build SQL /Importing SQL DATA
     *
     * @param string $args as the queries of sql data , yopu could use file get contents to read data args
     * @param string $dbhost database host
     * @param string $dbuser database user
     * @param string $dbpass database password
     * @param string $dbname database name     
     * @return string complete if complete
     */

    private function import_MysqliDatabase($args, $dbhost, $dbuser, $dbpass, $dbname) {

        // check mysqli extension installed
        if (!function_exists('mysqli_connect')) {
            die(' This scripts need mysql extension to be running properly ! please resolve!!');
        }

        $mysqli = @new mysqli($dbhost, $dbuser, $dbpass, $dbname);

        if ($mysqli->connect_error) {
            print_r($mysqli->connect_error);
            return false;
        }
        $mysqli->set_charset("utf8");
        $querycount = 11;
        $queryerrors = '';
        $lines = (array) $args;
        if (is_string($args)) {
            $lines = array($args);
        }

        if (!$lines) {
            return '' . 'cannot execute ' . $args;
        }

        $scriptfile = false;
        foreach ($lines as $line) {
            $line = trim($line);
            // if have -- comments add enters
            if (substr($line, 0, 2) == '--') {
                $line = "\n" . $line;
            }
            if (substr($line, 0, 2) != '--') {
                $scriptfile .= ' ' . $line;
                continue;
            }
        }

        $queries = explode(';'.PHP_EOL, $scriptfile);
        foreach ($queries as $query) {
            $query = trim($query);
            ++$querycount;

            if ($query == '') {
                continue;
            }


            if (!$mysqli->query($query)) {
                $queryerrors .= '' . 'Line ' . $querycount . ' - ' . $mysqli->error . '<br>';
                continue;
            }
        }


        if ($queryerrors) {
            return '' . 'There was an error on File: ' . $this->filename . '<br>' . $queryerrors;
        }

        if ($mysqli && !$mysqli->error) {
            @$mysqli->close();
        }

        return 'complete dumping database !';
    }

    /**
     * MYSQL EXPORT TO GZIP 
     * exporting database to sql gzip compression data.
     * if directory writable will be make directory inside of directory if not exist, else wil be die
     *
     * @param string directory , as the directory to put file
     * @param $outname as file name just the name !, if file exist will be overide as numeric next ++ as name_1.sql.gz , name_2.sql.gz next ++
     *
     * @param string $dbhost database host
     * @param string $dbuser database user
     * @param string $dbpass database password
     * @param string $dbname database name
     *
     */
    private function backup_MysqliDatabase($directory, $outname, $dbhost, $dbuser, $dbpass, $dbname, $tables) {

        // check mysqli extension installed
        if (!function_exists('mysqli_connect')) {
            die(' This scripts need mysql extension to be running properly ! please resolve!!');
        }

        $mysqli = @new mysqli($dbhost, $dbuser, $dbpass, $dbname);

        if ($mysqli->connect_error) {
            print_r($mysqli->connect_error);
            return false;
        }
        $mysqli->set_charset("utf8");
        $dir = $directory;
        $result = '<p> Could not create backup directory on :' . $dir . ' Please Please make sure you have set Directory on 755 or 777 for a while.</p>';
        $res = true;
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 755)) {
                $res = false;
            }
        }

        $n = 1;
        if ($res) {

            $name = $outname;
            # counts
            if (file_exists($dir . '/' . $name . '.sql.gz')) {

                for ($i = 1; @count(file($dir . '/' . $name . '_' . $i . '.sql.gz')); $i++) {
                    $name = $name;
                    if (!file_exists($dir . '/' . $name . '_' . $i . '.sql.gz')) {
                        $name = $name . '_' . $i;
                        break;
                    }
                }
            }

            $fullname = $dir . '/' . $name . '.sql.gz'; # full structures

            if (!$mysqli->error) {
                if ($tables == NULL) {
                    $sql = "SHOW TABLES";
                    $show = $mysqli->query($sql);
                    while ($r = $show->fetch_array()) {
                        $tables[] = $r[0];
                    }
                }

                if (!empty($tables)) {                	
                    //cycle through
                    $return = '';
                    foreach ($tables as $table) {
                        $result = $mysqli->query('SELECT * FROM ' . $table);
                        $num_fields = $result->field_count;
                        $row2 = $mysqli->query('SHOW CREATE TABLE ' . $table);

                        $row2 = $row2->fetch_row();
                        $return .=
                                "\n
-- ---------------------------------------------------------
--
-- Table structure for table : `{$table}`
--
-- ---------------------------------------------------------

" . $row2[1] . ";\n";

                        for ($i = 0; $i < $num_fields; $i++) {

                            $n = 1;
                            while ($row = $result->fetch_row()) {


                                if ($n++ == 1) { # set the first statements
                                    $return .=
                                            "
--
-- Dumping data for table `{$table}`
--

";
                                    /**
                                     * Get structural of fields each tables
                                     */
                                    $array_field = array(); #reset ! important to resetting when loop 
                                    while ($field = $result->fetch_field()) { # get field
                                        $array_field[] = '`' . $field->name . '`';
                                    }
                                    $array_f[$table] = $array_field;
                                    // $array_f = $array_f;
                                    # endwhile
                                    $array_field = implode(', ', $array_f[$table]); #implode arrays

                                    $return .= "INSERT INTO `{$table}` ({$array_field}) VALUES\n(";
                                } else {
                                    $return .= '(';
                                }
                                for ($j = 0; $j < $num_fields; $j++) {

                                    $row[$j] = str_replace('\'', '\'\'', preg_replace("/\n/", "\\n", $row[$j]));
                                    if (isset($row[$j])) {
                                        $return .= is_numeric($row[$j]) ? $row[$j] : '\'' . $row[$j] . '\'';
                                    } else {
                                        $return.= '\'\'';
                                    }
                                    if ($j < ($num_fields - 1)) {
                                        $return.= ', ';
                                    }
                                }
                                $return.= "),\n";
                            }
                            # check matching
                            @preg_match("/\),\n/", $return, $match, false, -3); # check match
                            if (isset($match[0])) {
                                $return = substr_replace($return, ";\n", -2);
                            }
                        }

                        $return .= "\n";
                    }

                    $return = "-- ---------------------------------------------------------
--
-- SIMPLE SQL Dump	
-- http://www.nawa.me/
--
-- Edited By Ahsan Zahid Chowdhury
-- http://itszahid.info
--
-- Host Connection Info: " . $mysqli->host_info . "
-- Generation Time: " . date('F d, Y \a\t H:i A ( e )') . "	
-- PHP Version: " . PHP_VERSION . "
--
-- ---------------------------------------------------------\n\n

SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET time_zone = \"+00:00\";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `".$dbname."`
--
" . $return . "
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
	

                    # end values result
                    // echo $return;exit;

                    @ini_set('zlib.output_compression', 'Off');
                    $gzipoutput = gzencode($return, 9);

                    if (@ file_put_contents($fullname, $gzipoutput)) { # 9 as compression levels
                        $result = $name . '.sql.gz'; # show the name
                    } else { # if could not put file , automaticly you will get the file as downloadable
                        $result = false;
                        // various headers, those with # are mandatory
                        header('Content-Type: application/octet-stream');
                        header("Content-Description: File Transfer");
                        header('Content-Encoding: gzip'); #
                        header('Content-Length: ' . strlen($gzipoutput)); #
                        header('Content-Disposition: attachment; filename="' . $name . '.sql.gz' . '"');
                        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
                        header('Connection: Keep-Alive');
                        header("Content-Transfer-Encoding: binary");
                        header('Expires: 0');
                        header('Pragma: no-cache');

                        echo $gzipoutput;
                    }
                } else {

                    $result = '<p>Error when executing database query to export.</p>' . $mysqli->error;
                }
            }
        } else {
            $result = '<p>Wrong mysqli input</p>';
        }

        if ($mysqli && !$mysqli->error) {
            @$mysqli->close();
        }

        return $result;
    }

}

?>