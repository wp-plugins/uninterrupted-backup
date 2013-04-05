<?php
/**
* Request parameters class file.
*/

// No direct access.
defined('ABSPATH') or die(NO_DIRECT_ACCESS_MSG);

/**
* Provide access to the request parameters
* via OOP interface.
* 
* @author Ahmed Said.
*/
class UBP_Lib_Request {
	
	/**
	* Query string parameters.
	* 
	* @var Array
	*/
	protected $get;
	
	/**
	* List of all request objects created via getInstance method.
	* 
	* @var Array
	*/
	protected static $instances = array();
	
	/**
	* Post request parameters.
	* 
	* @var Array
	*/
	protected $post;
	
	/**
	* Initialize object instantiation.
	* 
	* Set both GET and POST request parameters.
	* 
	* @return void
	*/
	public function __construct() {
		// GET parameters.
		$this->get = is_array($_GET) ? $_GET : array();		
		// Assign POST parameters if available.
		$this->post = ($_SERVER['REQUEST_METHOD'] == 'POST') ? $_POST : array();
	}
	
	/**
	* Get parameter value by the giveen name and type.
	* 
	* @param String Parameter name.
	* @param String get OR post.
	*/
	public function get($name, $type = 'get') {
		// Get request array.
		$rParams = $this->{$type};
		return isset($rParams[$name]) ? $rParams[$name] : null;
	}
	
	/**
	* Get request object instance by name or create and associate it
	* wih the given name.
	* 
	* @param String Instance name.
	* @return UBP_Lib_Request Request object.
	*/
	public static function & getInstance($name = null) {
		// Fetch if exists!
		if (!isset(self::$instances[$name])) {
			// Only store instances with names, others are treated as unamanaged!!
			$instance = new UBP_Lib_Request();
			if ($name) {
				self::$instances[$name] = $instance;
			}
		}
		else {
			// Read by name!
			$instance = self::$instances[$name];
		}
		return $instance;
	}
	
	/**
	* Update/Add parameter value for request parameter
	* by the given name.
	* 
	* @param String Parameter name.
	* @param String New value.
	* @param String Type to be get OR post.
	* @return UBP_Lib_Request Returning $this.
	*/
	public function set($name, $value, $type = 'get') {
		// Set Request parameter.
		$this->{$type}[$name] = $value;
		// Chaining.
		return $this;
	}
	
} // End class.
