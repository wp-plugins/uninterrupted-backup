<?php
/**
* Setup UBP Plugin.
*/

// No direct access.
defined('ABSPATH') or die(NO_DIRECT_ACCESS_MSG);

/**
* Code for setup UBP Plugin.
* 
* The class functions is to always put UBP plugins
* as the first Plugin in active_plugins Wordpress option so it'll
* always loaded first.
* 
* @author Ahmed Said
*/
class UBP_Accesspoint_Setup {
		
	/**
	* Wordpress filter callback for 'pre_update_option_active_plugins'
	* filter.
	* 
	* Always put UBP Plugin as the first Plugin in the active
	* plugins list.
	* 
	* @param Array @activePlugins parameter with the UBP as first Plugin (@index 0).
	*/
	public function _preUpdateActivePlugins($activePlugins) {
		// Pass control to Setup Controller.
		$controller = new UBP_Controller_Setup();
		$controller->getRequest()->set('plugins', $activePlugins, 'post');
		// Run setup action.
		return $controller->doAction('setup')
		// Return new plugins order to Wordpress filter!
		->getResponse()->read('plugins');
	}

	/**
	* Bind a filter to run whenever
	* active_plugins Wordpress option is changed.
	* 
	* @return Bool TRUE when success, FLASE otherwise.
	*/
	public function bind() {
		// Change active plugins order every time actie_plugins
		// option updated.
		if (is_admin()) {
			add_filter('pre_update_option_active_plugins', array($this, '_preUpdateActivePlugins'), 11);
		}
		return TRUE;
	}
	
} // End class.