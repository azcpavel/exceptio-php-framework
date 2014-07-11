<?php
define('EXT','.php');
/**
 * Zend Framework Loader
 *
 * Put the 'Zend' folder (unpacked from the Zend Framework package, under 'Library')
 * in installation's 'system/libraries' folder
 * You can put it elsewhere but remember to alter the script accordingly
 *
 * 
 *   1) $this->load->library('zend');
 *      then $this->zend->load('Zend/Package');
 *
 * * the second usage is useful for autoloading the Zend Framework library
 * * Zend/Package/Name does not need the '.php' at the end
 */
class Zend
{
    /**
     * Constructor
     *
     * @param   string $class class name
     */
    function __construct()
    {
        ini_set('include_path', ini_get('include_path').':'.DOCUMENT_ROOT . BASEDIR . SYSTEM . '/libraries');
    }

    /**
     * Zend Class Loader
     *
     * @param   string $class class name
     */
    function load($class)
    {        
        require_once (string) ($class) . EXT;                
    }
}