<?php
/**
* Setup UBP class.
*/

// No direct access.
defined('ABSPATH') or die(NO_DIRECT_ACCESS_MSG);

/**
* Setup UBP Plugin operations class.
* 
* @author Ahmed Said
*/
class UBP_Controller_Setup extends UBP_Lib_Mvc_Controller {
	
	/**
	* Integrate UBP with Wordpress.
	* 
	* Add UBP Plugin at the first of the active_plugins queue list,
	* so it'll be always executed first and therefor avble to
	* disable EIP before breaking down the site.
	* 
	* @return void
	*/
	public function setup() {
		// Initialize.
		$request =& $this->getRequest();
		$response =& $this->getResponse();
		// Get UB Plugin main file relative path to UB Plugin.
		$ubpMFRPath = basename(dirname(UBP::FILE)) . '/' . basename(UBP::FILE);
		// Read plugins!
		$plugins = $request->get('plugins', 'post');
		// Always put the plugin as the first item if its not!
		$ubpPluginAtIndex = array_search($ubpMFRPath, $plugins);
		if ($ubpPluginAtIndex) {
			// Remove!
			unset($plugins[$ubpPluginAtIndex]);
			array_unshift($plugins, $ubpMFRPath);
			// Re-indexing!
			$plugins = array_values($plugins);
		}
		// Response back.
		$response->write('plugins', $plugins);
	}

} // End class.
