<?php
/**
 * Plugin Name: Frontend Dashboard Extra Plus
 * Plugin URI: https://github.com/fetitxe/frontend-dashboard-extra-plus
 * Description: Frontend Dashboard Extra Plus WordPress plugin is a supportive plugin for Frontend Dashboard.
 * Version: 1.3.0
 * Author: fetitxe
 * Author URI: https://github.com/fetitxe/frontend-dashboard-extra-plus
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html 
 * Text Domain: fed
 *
 * @package frontend-dashboard-extra-plus
 */

if( !defined('ABSPATH') ) exit;

$fed_check = get_option('fed_plugin_version');

require_once ABSPATH . 'wp-admin/includes/plugin.php';

if( $fed_check && is_plugin_active('frontend-dashboard/frontend-dashboard.php') ){

	/**
	 * Version Number
	 */
	define('FED_EXTRA_PLUS_PLUGIN_VERSION', '1.3.0');

	/**
	 * App Name
	 */
	define('FED_EXTRA_PLUS_APP_NAME', 'Frontend Dashboard Extra Plus');

	/**
	 * Root Path
	 */
	define('FED_EXTRA_PLUS_PLUGIN', __FILE__);

	/**
	 * Plugin Base Name
	 */
	define('FED_EXTRA_PLUS_PLUGIN_BASENAME', plugin_basename(FED_EXTRA_PLUS_PLUGIN));

	/**
	 * Plugin Name
	 */
	define('FED_EXTRA_PLUS_PLUGIN_NAME', trim(dirname(FED_EXTRA_PLUS_PLUGIN_BASENAME), '/'));

	/**
	 * Plugin Directory
	 */
	define('FED_EXTRA_PLUS_PLUGIN_DIR', untrailingslashit(dirname(FED_EXTRA_PLUS_PLUGIN)));

	require_once FED_EXTRA_PLUS_PLUGIN_DIR . '/menu/class_fed_extra_plus_menu.php';
	require_once FED_EXTRA_PLUS_PLUGIN_DIR . '/functions.php';

}else{
	add_action('admin_notices', function(){
		?><div class="notice notice-warning">
			<p><b><?php _e('Please install <a href="https://github.com/fetitxe/frontend-dashboard">my Frontend Dashboard fork</a> to use this plugin [Frontend Dashboard Extra Plus]', 'frontend-dashboard-extra-plus'); ?></b></p>
		</div><?php
	});
}