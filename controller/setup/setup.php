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
	* Wordpress-Plugins relative path to UBP Plugin
	* main file.
	* 
	*/
	const UBP_PLUGIN_REL_FILE = 'ubp/ubp.php';
	
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
		// Read plugins!
		$plugins = $request->get('plugins', 'post');
		// Always put the plugin as the first item!
		$ubpPluginAtIndex = array_search(self::UBP_PLUGIN_REL_FILE, $plugins);
		if ($ubpPluginAtIndex) {
			// Remove!
			unset($plugins[$ubpPluginAtIndex]);
			array_unshift($plugins, self::UBP_PLUGIN_REL_FILE);
			// Re-indexing!
			$plugins = array_values($plugins);
		}
		// Response back.
		$response->write('plugins', $plugins);
	}

} // End class.
