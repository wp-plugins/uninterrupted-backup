<?php
/**
* Automatic classes loading for UBP Plugin
*
*/

// No direct access.
defined('ABSPATH') or die(NO_DIRECT_ACCESS_MSG);

/**
* Manage auto loads for UBP classes.
* 
* Any number of instances can be created from this class.
* Every instance has a base-path and prefix to jailed to.
* The class then map the requested class file from the class name
* as each 'Word between '_ character is a directrory name and the final 'Word'
* is the class directory and cthe lass file name too.
* 
* Warning: If multiple instances with the same 'Preifx' created then there're multiple
* auto load methods registered and therefore multiple methods
* will try to load the same class. The class is not tested to be used
* with multiple instances but allow that! However future version would
* has more enhancements to this class and the mapping mechanism.
* 
* @author Ahmed Said.
*/
class UBP_Lib_Classloader {
	
	/**
	* Separator char used to get class name components.
	*/
	const SEPERATOR = '_';
	
	/**
	* Absolute base path to map the requested class file.
	* 
	* @var String Default to empty string.
	*/
	protected $basePath = '';
	
	/**
	* Manage UBP_Lib_Classloader instances only when
	* created by calling getInstance() method.
	* 
	* @var Array Default to empty array.
	*/
	protected static $instances = array();
	
	/**
	* Class prefix used to detect what classes the class
	* auto-load method should handle.
	* 
	* @var String Default is empty string.
	*/
	protected $prefix = '';
	
	/**
	* PHP SPL registered auto load callback method.
	* 
	* Call this metod directly only if you know what you do.
	* 
	* @param String class name
	* @return void
	*/
	public function _autoLoad($className) {
		$signature = $this->prefix . self::SEPERATOR;
		// Test class name if its laying under our PREFIX!
		if (strpos($className, $signature) !== FALSE) {
			$classFile = $this->getClassFile($className);
			// Import class file.
			include_once $classFile;
		}
	}
	
	/**
	* Initialize object creation.
	* 
	* @param String Absolute base path to the directory to map the prefixed class to.
	* @param String Classes prefix to be handled by the auto-load method.
	* @return void
	*/
	public function __construct($basePath, $prefix) {
		// Initialize!
		$this->basePath = $basePath;
		$this->prefix = $prefix;
		// Register auto load!
		spl_autoload_register(array($this, '_autoLoad'));
	}
	
	/**
	* Build PHP class name from the given type and name.
	* 
	* If you've organized your classes under various categories/directories,
	* then this method can get you a class name from the relative maps.
	* 
	* Example: IF type = database/mysql and name = table
	* then the final class name is [PREFIX]_database_mysql_table
	* 
	* @param String Relative path to the class.
	* @param String Class name.
	*/
	public function buildClassName($type, $name) {
		// Initialize.
		$classComponents = array();
		// Build class name array.
		$classComponents['prefix'] = $this->prefix;
		// Replace '/' with underscore (/ is not directory separator, its just our choice!)
		$classComponents['type'] = ucfirst(str_replace(array('/'), self::SEPERATOR, $type));
		$classComponents['name'] = ucfirst($name);
		// Build full name with separator.
		$className = implode(self::SEPERATOR, $classComponents);
		return $className;
	}
	
	/**
	* Get base path for the autoloader class.
	* 
	* @return String Absolute base path for class autoloader.
	*/
	public function getBasePath() {
		return $this->basePath;	
	}
	
	/**
	* Get class Absolute file from the requested class name.
	* 
	* The method doesn't do any kind of check if the path is valid or not.
	* Even if this function is called with nivalid prefix it'll still return a file path.
	* 
	* @param String Class name to map to a file.
	* @return String Class file.
	*/
	public function getClassFile($name) {
		// Parse name components.
		$components = explode(self::SEPERATOR, $name);
		// Remove prefix as it represent the root path to Plugin!
		unset($components[0]);
		// Get class folder relative path.
		$classFolder = implode(DIRECTORY_SEPARATOR, $components);
		// Get full path to class file.
		$fileName = end($components);
		$classFile = $classFolder . DIRECTORY_SEPARATOR . "{$fileName}.php";
		// Absolute path to the file!
		$absPath = $this->basePath . DIRECTORY_SEPARATOR . $classFile;
		return $absPath;
	}
	
	/**
	* Get class name components.
	* 
	* The returned array has the following properties.
	* 
	* 	- @prefix: Class prefix (First entity).
	* 	- @file: File name with no extension added (Last entity).
	* 	- @path: Relative path to the class (all the entities between Prefix and Class-Name entities
	* 								concated with the DIRECTORY_SEPARATOR Char).
	* 
	* @param Array Class name components.
	*/
	public function getClassNamePathComponent($class) {
		// Initialize.
		$rawComponents = array();
		$components = array();
		// Get class name if object provided!
		if (is_object($class)) {
			$class = get_class($class);
		}
		// Parse name components.
		$rawComponents = explode(self::SEPERATOR, $class);
		// Get associative/named component instead of indexed!
		$components['prefix'] = strtolower($rawComponents[0]);
		$components['file'] = strtolower(end($rawComponents));
		$components['path'] = '/'; // Default path, if it has no path!
		// Get Path!
		for ($index = 1; ($index < count($rawComponents) - 1); $index++) {
			$components['path'] .= strtolower($rawComponents[$index]) . DIRECTORY_SEPARATOR;
		}
		return ((object) $components);
	}
	
	/**
	* Create or Get class laoded instance by name.
	* 
	* @param String name to be associated with the newly created instance
	* 												or the instance to returned.
	* @param String Base Path @see __construct
	* @param String Prefix @see __construct
	* @return UBP_Lib_Classloader Class loader instance.
	*/
	public static function & getInstance($name = null, $basePath = null, $prefix = '') {
		// Check if exists!
		if (isset(self::$instances[$name])) {
			$loader = self::$instances[$name];
		}
		else {
			$loader = new UBP_Lib_Classloader($basePath, $prefix);
			// Store it if created with name!
			self::$instances[$name] = $loader;
		}
		// Get specific loader!
		return $loader;
	}
	
	/**
	* Instantiate a class under specific type and name.
	* 
	* @param string Type of the class -- relative path below BasePath to the class name!
	* @param string Class name.
	* @param array Parameters to be passed to the class getInstance method.
	* @return mixed Target class object instance
	*/
	public function getInstanceOf($type, $name, $parameters = null) {
		// Defaults.
		if (!is_array($parameters)) {
			$parameters = array();
		}
		// Build class name.
		$className = $this->buildClassName($type, $name);
		// If has no getInstance static method do normal construction.
		if (method_exists($className, 'getInstance')) {
			// Get instance method implemeted and free to use
			// args list.
			$instance = call_user_func_array(array($className, 'getInstance'), $parameters);	
		}
		else {
			// Regular construction call with force to use single array args!
			$instance = new $className($parameters);
		}
		return $instance;
	}
	
	/**
	* Get prefix aassociated with current Class Loade class.
	* 
	* @return String Prefix.
	*/
	public function getPrefix() {
		return $this->prefix;	
	}
	
} // End class.