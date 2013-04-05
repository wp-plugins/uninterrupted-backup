<?php
/**
* Response class file.
*/

// No direct access.
defined('ABSPATH') or die(NO_DIRECT_ACCESS_MSG);

/**
* Define a standard way for objects to talk
* and interacting with each others.
* 
* @author Ahmed Said
*/
class UBP_Lib_Response {
	
	/**
	* List of the dynamically (run-time) defined
	* list of parameters created by objects.
	* 
	* @var Array
	*/
	protected $stores = array();
	
	/**
	* Instantiate UBP_Lib_Response class.
	* 
	* @return UBP_Lib_Response New Instance.
	*/
	public static function & getInstance() {
		$instance = new UBP_Lib_Response();
		return $instance;
	}
	
	/**
	* Read stored value.
	* 
	* @param mixed Stored value if exists or @default.
	* @param mixed Default value to return in case the store not exists.
	* @return mixed Store or Default value.
	*/
	public function read($store, $default = null) {
		return isset($this->stores[$store]) ? $this->stores[$store] : $default;
	}
	
	/**
	* Write store data.
	* 
	* @param String Store name.
	* @param mixed Store value.
	* @return UBP_Lib_Response Returning $this.
	*/
	public function write($store, $value) {
		// Store value.
		$this->stores[$store] = $value;
		// Chaining.
		return $this;
	}
	
} // End class.