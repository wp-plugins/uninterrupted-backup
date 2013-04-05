<?php
/**
* Backup keys management class.
*/

// No direct access.
defined('ABSPATH') or die(NO_DIRECT_ACCESS_MSG);

/**
* Manage Secure Backup keys for UBP Plugin
* 
* From here you can generate, release,
* check, validate and search Backup keys.
* 
* @author Ahmed Said
*/
class UBP_Lib_Backupkeys {
	
	/**
	* Backup key life-time in seconds.
	*/
	const KEY_LIFE_TIME_SEC = 86400;
	
	/**
	* Wordpress option_name for UBP backup secure keys list.
	*/
	const KEYS = 'ubp_secure_access_key';
	
	/**
	* Backup keys list.
	* 
	* The array keys is the relative path to Wordpress-Plugins directory
	* for the target Plugin (Plugin with the error).
	* 
	* The value of the array is array element (key)
	* with the following properties:
	* 
	* 	- @hash String: Secure backup key.
	* 	- @time: Unix timestamp for the key generated time.
	* 	- @plugin: Wordpress Plugin data
	* 		- @Ref: Wordpress Plugin data returned by Wordpress get_plugin_data() function.
	* 		- @RelFile: Plugin relative path to Wordpress-Plugins directory.
	* 
	* @var array
	*/
	protected $keys;
	
	/**
	* Initialize class properties.
	* 
	* Use getInstance() for getting object instance
	* as this class is singlton class.
	* 
	* @return void
	*/
	protected function __construct() {
		// Cache Backup keys!
		$this->keys = get_option(self::KEYS, array());
	}
	
	/**
	* Generate new Backup secure key for the requested Plugn.
	* 
	* @param Array Plugin data.
	* @return array The new generated key.
	*/
	public function generate($plugin) {
		// MD5 for time with the unique salt!
		$key['hash'] = md5(time() . AUTH_SALT);
		$key['time'] = time();
		$key['plugin'] = $plugin;
		// Add key database, saved by name!
		$this->keys[$plugin['RelFile']] = $key;
		return $key;
	}
	
	/**
	* Get key for the given hash.
	* 
	* Search all the exists available keys
	* for the given hash and return the full key
	* array.
	* 
	* The method is only search for the key and not checking the validity of the hash.
	* 
	* @param String Hash key.
	* @return Array|NULL Key array or null if the hash not exists.
	*/
	public function getHashKey($hash) {
		// Initialize.
		$key = null;
		// Search keys for the provided hash!
		foreach ($this->keys as $sKey) {
			if ($sKey['hash'] == $hash) {
				$key = $sKey;
				break;
			}
		}
		return $key;
	}
	
	/**
	* Get UBP_Lib_Backupkeys object.
	* 
	* Please note the returned instance is almost shared among all the callers.
	* Only one instance is allwed.
	* 
	* @return UBP_Lib_Backupkeys Keys object.
	*/
	public static function & getInstance() {
		// Don't allow multiple instances!
		static $instance = null;
		// Instantiate!
		if (!$instance) {
			$instance = new UBP_Lib_Backupkeys();
		}
		return $instance;
	}
	
	/**
	* Get Backup secure key from Plugin relative file.
	* 
	* The method check the existance of the key 
	* from Plugin relative path.
	* 
	* @param String Plugin relative file.
	* @return Array/FALSE Backup key or FALSE if not exists.
	*/
	public function getPluginKey($rFile) {
		return isset($this->keys[$rFile]) ? $this->keys[$rFile] : FALSE;
	}
	
	/**
	* Check the validity of the key.
	* 
	* Check if the backup is exists and not expired.
	* 
	* @param Array Backup key.
	* @return Boolean TRUE if valid or FALSE otherwise.
	*/
	public function isValid($key) {
		// Check if the key generation time has passed the LIFE_TIME seconds.
		return ((time() - $key['time']) < self::KEY_LIFE_TIME_SEC);
	}
	
	/**
	* Release/Delete the given key from the cached keys-list.
	* 
	* @param Array Key to release.
	* @return TRUE if deleted (exists), FALSE otherwise.
	*/
	public function release($key) {
		// Initialize.
		$released = false;
		// Code it well!
		if (isset($key['plugin']['RelFile'])) {
			// Check weather the key is exists!
			$pluginRelFile = $key['plugin']['RelFile'];
			if (isset($this->keys[$pluginRelFile])) {
				// Remove from list!
				unset($this->keys[$pluginRelFile]);
				// Notify!
				$released = true;
			}
		}
		return $released;
	}
	
	/**
	* Save all the local (cache) changes to the backup keys
	* into the database
	* 
	* @return UBP_Lib_Backupkeys Return $this.
	*/
	public function save() {
		// Save into database.
		update_option(self::KEYS, $this->keys);
		// Chaining!
		return $this;
	}
	
} // End class.
