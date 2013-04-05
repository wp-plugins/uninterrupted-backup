<?php
/**
* UB Plugin constants.
*/

/**
* No direct access message.
*/
define('UBP', 'NO_DIRECT_ACCESS_MSG', 'Direct access is not allowed! This file cannot be executed directly by called from the browser!!');

// No direct access.
defined('ABSPATH') or die(NO_DIRECT_ACCESS_MSG);
