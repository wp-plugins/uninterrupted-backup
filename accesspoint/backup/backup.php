<?php
/**
* Holds code for back-up/disable plugins.
*/

// No direct access.
defined('ABSPATH') or die(NO_DIRECT_ACCESS_MSG);

/**
* Backup Access Point class for
* executing or listen for backup request.
* 
* @author Ahmed Said
*/
class UBP_Accesspoint_Backup {

	/**
	* Open the access point and put it in action.
	* 
	* @return bool TRUE when success, FALSE if fail.
	*/
	public function bind() {
		// Initialize.
		$request =& UBP_Lib_Request::getInstance();
		// Run only if not in backup mode!
		if ($key = $request->get('ubp_backup_key')) {
			// Backup site!
			$controller = new UBP_Controller_Backup();
			$controller->doAction('backup');
		}
		return $key ? true : false;
	}
	
} // End class.
