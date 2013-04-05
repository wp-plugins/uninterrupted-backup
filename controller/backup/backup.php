<?php
/**
* Handle backup/recover operations.
*/

// No direct access.
defined('ABSPATH') or die(NO_DIRECT_ACCESS_MSG);

/**
* Code to handle backup operation.
* 
* Here is where the UBP Plugin handle the request for disable
* the error-issue-plugin (EIP).
* 
* @author Ahmed Said
*/
class UBP_Controller_Backup extends UBP_Lib_Mvc_Controller {

	/**
	* Handle backup/disable-plugin action.
	* 
	* Check the validity of backup key. Display user message
	* to reflect the validity of the backup key and of the backup process state.
	* 
	* @param string ubp_backup_key Backup key for EIP.
	* 
	* @return void
	*/
	public function backup() {
		// Initialize.
		$message = '';
		$keys = UBP_Lib_Backupkeys::getInstance();
		// Check if needed to backup!
		if ($hash = $this->getRequest()->get('ubp_backup_key')) {
			// Check if valid key + fetch corresponding Plugin/Key!
			if (!$key = $keys->getHashKey($hash)) {
				$message =  'Access Denied!';
			}
			else {
				// Key is expired!
				if (!$keys->isValid($key)) {
					$message = 'Invalid key!';
				}
				else {
					// Disable plugin!!
					require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'plugin.php';
					deactivate_plugins($key['plugin']['RelFile'], true);
					// Delete Backup key!
					if ($keys->release($key)) {
						// Save into database!
						$keys->save();
					}
					// User Notification.
					$message = 'Plugin deactivated! Reloading the page will cause the site to load without the arget Plugin!';
				}
			}
			// Terminate with message.
			die($message);
		}
	}
	
} // End class.
