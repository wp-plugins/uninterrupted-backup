=== Uninterrupted Backup ===
Tags: uninterrupted, backup, recovery, disable, backdoor, error, blocked, fatal, deactivate, provide, access, path
Requires at least: 3.5
Tested up to: 3.5
Stable tag: 0.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Detect fatal errors that might be produced by the installed active Plugins, provide secure backdoor for deactivating the target Plugin.

== Description ==
                                   
= Purpose =
Detect Wordpress Plugins 'Fatal Errors' that might block/prevent site admins from accessing Wordpress admin interface in order to deactivate the Plugin that caused the issue!
UBP then report/notify (sedning email) site admin with a secure link that valid for 24 hours from which site admin can deactivate the target Plugin by simply clicking the link!

For development state and issue tracking follow the Plugin on [GitHub Respository](https://github.com/xpointer/ubp)

= Mechanism =
The mechanism of the Plugin is to always put itself as the first Plugin to be executed by Wordpress Plugins system! The Plugin then just stay away and never get itself involved
until the execution of all the other scripts is then terminated! UBP Plugin role is comes when the script is terminated. It then check if the script is terminated with error. If the script
if terminated with error UBP send admin mail with backup link that has a secure key by which UBP give the link owner the permission to disable the target Plugin.

= Advantages =
* There is no need to access throught FTP to force Wordpress deactivate the Plugin. You can always use this Plugin to deactivate the target Plugin only with the link sent with the mail.
* Load minimum codes/scripts that required only to get involved (listen to errors). Load code required to functional only when error detcted.

= Notes =
* Whenever you active or deactive a Plugin, UBP will always put itself as the first Plugin in Wordpress active Plugins queue.
* The Plugin won't install itself while the site is already blocked by the error.
* UBP must be installed first while the site is fully functional and then it'll help you backing up your site if any newly Plugin produced an error.
* It Won't help you on all type of errors you've in your Wordpress site however it can always give you the chance to get your site functional again after installing any Plugin that prevent you from accessing your backend.
* It has no admin user interface and therefor not configurable, however next versions will do.
* Send admin mail whatever 'fatal errors' is detected and it doesn't matter if this error blocked your site or not. It'll also report fatal errors that might happened in a secondary request that doesn't block you from deacivating the Plugin yourself.

= Proof Of Concept = 
I still have many useful ideas and feature to add to the Plugin however the Plugin need users reviews to proof that its really helping and its really need to be improved.

== Installation ==

1. Upload `ubp` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 0.1.2 =
* Rename main file to has the same name as Plugin directory as Plugin directory changed to uninterrupted-backup due to the Plugin name changed.
* Fix that searching for UBP Plugin by ts file always fail as the Plugin directory  name changed, dynamically get UB Plugin relative path file.

= 0.1 =
* First beta release.