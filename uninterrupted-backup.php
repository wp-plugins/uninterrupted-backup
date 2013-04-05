<?php
/**
* Plugin Name: Uninterrupted Backup
* Plugin URI: http://ubp.wpp.longestvision.com/
* Author: Ahmed Said
* Author URI: http://ubp.wpp.longestvision.com/
* Description: Detect fatal errors that might be produced by the installed active Plugins, provide secure backdoor for deactivating the target Plugin.
* Version: 0.1.2
* License: GPLv2 or later
*/

// Import pre-load constants.
require_once 'defines.inc.php';

// No direct access.
defined('ABSPATH') or die(NO_DIRECT_ACCESS_MSG);

/**
* UBP main class. Load all access points used by UBP Plugin.
* 
* @author Ahmed Said
*/
abstract class UBP {

	/**
	* Absolute path to UBP Plugin main file (__FILE__).
	* 
	*/
	const FILE = __FILE__;
		
	/**
	* UBP Plugin prefix/signature used for prefixing
	* all UBP classes.
	* 
	*/
	const NAME = 'UBP';

	/**
	* UBP access points data to be binded.
	* 
	* @var array
	*/
	protected static $accesspoints = array(
		array('name' => 'setup'),
		array('name' => 'error'),
		array('name' => 'backup')
	);
	
	/**
	* UBP entry point method.
	* 
	* @return void
	*/
	public static function main() {
		// Auto load UBP classes!
		include_once 'lib/classloader/classloader.php';
		$loader = UBP_Lib_Classloader::getInstance(null, dirname(__FILE__), self::NAME);
		// Controllers to be always loaded!
		foreach (self::$accesspoints as $accesspoint) {
			$name = $accesspoint['name'];
			// Access Point class name from name.
			self::$accesspoints[$name]['instance'] = $loader->getInstanceOf('accesspoint', $name)
																																											->bind();
		}
	}

} // End class.

// Action!
UBP::main();