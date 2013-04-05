<?php
/**
* Detecting Plugins error class.
*/

// No direct access.
defined('ABSPATH') or die(NO_DIRECT_ACCESS_MSG);

/**
* Handle error detecting operations.
* 
* Detect Plugins error! Manage backup key generation.
* 
* @author Ahmed Said
*/
class UBP_Controller_Error extends UBP_Lib_Mvc_Controller {

	/**
	* Check if the error is from a Plugin file.
	* Generate backup key if the target Plugin
	* is not already has one generated and not expired.
	* 
	* @return void
	*/
	public function error() {
		// Initialize!
		$model =& $this->getModel();
		// Read last error!
		$error = error_get_last();
		// Take action only if the error produced by Plugin!
		if ($plugin = $model->getPluginFromErrorFile($error['file'])) {
			// Generate backup key, send mail only if there is no key exists for the same Plugin!
			if ($key = $model->genBackupKey($plugin)) {
				// Send administrator mail with backup link!!
				$model->eMailAdmin($key, $plugin);
			}
		}
	}

} // End class.