<?php
/**
* Controller base class.
*/

// No direct access.
defined('ABSPATH') or die(NO_DIRECT_ACCESS_MSG);

/**
* Handling common operations required
* for instantiating controller classes and for
* controllers object to interact with the MVC.
* 
* @author Ahmed Said
*/
class UBP_Lib_Mvc_Controller {
	
	/**
	* Request object tha defined the request parameters 
	* for the controller.
	* 
	* @var UBP_Lib_Request
	*/
	protected $request;
	
	/**
	* Response object to define the output/returns
	* from the current controller.
	* 
	* @var UBP_Lib_Response
	*/
	protected $response;
	
	/**
	* Initialize object.
	* 
	* @return void
	*/
	public function __construct() {
		// Instantiate new request and response objects for the controller.
		$this->request =& UBP_Lib_Request::getInstance();
		$this->response =& UBP_Lib_Response::getInstance();
	}
	
	/**
	* Dispatch child controller action by the given name.
	* 
	* @param String action name.
	* @return UBP_Lib_Mvc_Controller $this.
	*/
	public function doAction($action) {
		// @TODO: Use 'Action' word as trailer for evey action/method name.
		// Simple action dispatching!
		$this->{$action}();
		// Chaining.
		return $this;
	}
	
	/**
	* Get model object by name.
	* 
	* @param String model name.
	* @return Object Model object.
	*/
	public function & getModel($name = null) {
		// Initialize!
		static $models = array();
		$loader =& UBP_Lib_Classloader::getInstance();
		// Defaults.
		if (!$name) {
			$name = $loader->getClassNamePathComponent($this)->file;
		}
		// Get cached or create one!
		$models[$name] = !isset($models[$name]) ? $loader->getInstanceOf('model', $name) :
																																								$models[$name];
		return $models[$name];
	}
	
	/**
	* Get request object associated with the controller.
	* 
	* @return UBP_Lib_Request
	*/
	public function & getRequest() {
		return $this->request;	
	}
	
	/**
	* Get response object associated with the controller.
	* 
	* @return UBP_Lib_Response
	*/
	public function & getResponse() {
		return $this->response;	
	}
	
} // End class.
