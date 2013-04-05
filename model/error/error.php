<?php
/**
* Internal 'Error' functios class.
*/

// No direct access.
defined('ABSPATH') or die(NO_DIRECT_ACCESS_MSG);

/**
* Model for handling and detecting Errors.
* 
* @author Ahmed Said
*/
class UBP_Model_Error {
	
	/**
	* Send web master email when a new error
	* has been detected.
	* 
	* Create mail subject and message with a backup link
	* , send them to asite admin mail as configured in Wordpress
	* backend general settings page.
	* 
	* @param Array Backup secure key.
	* @param Array Plugin data.
	* @return Boolean TRUE is mail being send, FALSE otherwise.
	*/
	public function eMailAdmin($key, $plugin) {
		// Mail fields.
		$adminMail = get_bloginfo('admin_email');
		// Build backup link!
		$backupLink = get_bloginfo('wpurl') . "/?ubp_backup_key={$key['hash']}";
		// Subject!
		$subject = 'UBP Plugin error detected!';
		// Mail message.
		$message  = "UBP has detecting Blocked error in your site!\n";
		$message .= "Use {$backupLink} link to backup/disable the target Plugin\n\n";
		$message .= "######### Plugin Data ##########\n\n";
		// Add Plugin details into message.
		foreach ($plugin as $name => $value) {
			$message .= "{$name} = {$value}\n";
		}
		// Send mail.
		require_once ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'pluggable.php';
		$result = wp_mail($adminMail, $subject, $message);
		return $result;
	}
	
	/**
	* Generate backup key.
	* 
	* Generate key for a Plugin only if there
	* is no key exists for the same Plugin, results to
	* send error mail until the key is expired.
	* 
	* The main purpose to don't bother site admin with 
	* many error mail everytime a page is loaded.
	* 
	* @param Array Plugin data.
	* @return Array Backup Key with @plugin data injected.
	*/
	public function genBackupKey($plugin) {
		// Initialize.
		$key = FALSE;
		$keys =& UBP_Lib_Backupkeys::getInstance();
		// Generate key only if there is NO, VALID key exists!
		if ((!$dbKey = $keys->getPluginKey($plugin['RelFile'])) || !$keys->isValid($dbKey)) {
			// Generate new key or override an exists invalid key!
			$key = $keys->generate($plugin);
			// Saving new key!
			$keys->save();
		}
		return $key;
	}
	
	/**
	* Get Plugin relative path to the main file from
	* the given plugin directory name.
	* 
	* Search active_plugtins Wordpress option keys
	* to find a Plugin path that start up with @pluginDirectory
	* ended with a slash.
	* 
	* @param String Plugin directory.
	* @return string Relative path to Plugin main file.
	*/
	public function getPluginMainFile($PluginDirectory) {
		// Initialize.
		$pluginFile = FALSE;
		// Read all active plugins.
		$acPlugins = get_option('active_plugins');
		if (!is_array($acPlugins)) {
			$acPlugins = array();
		}
		// Search key if the plugin dir name with / appened
		// to ensure its not another plugin start with the same name (e.g test, test-2)!
		$searchKey = "{$PluginDirectory}/";
		// Search 'active_plugins' to find the plugin main file path.
		foreach ($acPlugins as $cRelFile) {
			// Plugin found!
			if (strpos($cRelFile, $searchKey) === 0) {
				// Build OS-Based path!
				$pluginFile = $PluginDirectory . DIRECTORY_SEPARATOR . basename($cRelFile);
				break;
			}
		}
		return $pluginFile;
	}
	
	/**
	* Get Plugin dara from a given file.
	* 
	* The passed file is almost a file whereis the error
	* is occured. The method goal is to get the Plugin data from
	* that file.
	* 
	* @see UBP_Model_Error::isPluginFile()
	* 
	* @param String Absolute path to file.
	* @return Array/NULL Plugin data or NULL if the file is not belong to a Plugin.
	*/
	public function getPluginFromErrorFile($file) {
		// Inijtialize.
		$plugin = null;
		// Get plugin data only if the provided file is for Plugin.
		if ($pluginFile = $this->isPluginFile($file)) {
			// Import get_plugin_data php file!
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'plugin.php';
			// Get Plugin data.
			$plugin = get_plugin_data($pluginFile['abs'], false);
			// Push rel file into Plugin data.
			$plugin['ErrorFile'] = $file;
			$plugin['File'] = $pluginFile['abs'];
			$plugin['RelFile'] = $pluginFile['rel'];
		}
		return $plugin;
	}
	
	/**
	* Check weather the given file is belong (laying
	* under Wordpress-Plugin directory) to a Plugin.
	* 
	* The method mask the passed file with Wordpress-Plugins
	* directory path and check the result. If there is remaining path
	* then the file is belong to a Plugion otherwise its not!!
	* 
	* @param String Absolute path to file.
	* @return String/FALSE Relative path a Plugin or FALSE if 
	* 																	file is not belong to a Plugin.
	*/
	public function isPluginFile($file) {
		$pluginFile = FALSE;
		/// Get OS-Based Plugins directory path! ///
		$pathToPluginsDir = dirname(dirname(UBP::FILE));
		// Check if the error file is belong to a Plugin!
		if (strpos($file, $pathToPluginsDir) === 0) {
			// Remove plugins directory path.
			$relFile = str_replace($pathToPluginsDir, '', $file);
			// Get Plugin dir name by splitting path parts.
			$relFileArray = explode(DIRECTORY_SEPARATOR, $relFile); 
			$pluginDirName = $relFileArray[1];
			// Get absolute paths to Plugin file.
			$pluginFileRelPath = $this->getPluginMainFile($pluginDirName);
			// Get Plugin base file.
			$pluginFile = array();
			$pluginFile['rel'] = $pluginFileRelPath;
			$pluginFile['abs'] = $pathToPluginsDir . DIRECTORY_SEPARATOR . $pluginFileRelPath;
		}
		return $pluginFile;
	}
	
} // End class.
