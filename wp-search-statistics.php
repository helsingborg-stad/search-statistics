<?php

/**
 * Plugin Name:       WP Search Statistics
 * Plugin URI:
 * Description:       Enhanced search with synonyms (if using ElasticPress) and statistics
 * Version: 1.0.3
 * Author:            Kristoffer Svanmark
 * Author URI:
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       wp-search-statistics
 * Domain Path:       /languages
 */

 // Protect agains direct file access
if (! defined('WPINC')) {
    die;
}

//Default value of constants
if (!defined('LOCAL_SITE_STATS')) {
    define('LOCAL_SITE_STATS', false);
}

define('SEARCHSTATISTICS_PATH', plugin_dir_path(__FILE__));
define('SEARCHSTATISTICS_URL', plugins_url('', __FILE__));
define('SEARCHSTATISTICS_TEMPLATE_PATH', SEARCHSTATISTICS_PATH . 'templates/');

load_plugin_textdomain('wp-search-statistics', false, plugin_basename(dirname(__FILE__)) . '/languages');

// Autoload from plugin
if (file_exists(SEARCHSTATISTICS_PATH . 'vendor/autoload.php')) {
    require_once SEARCHSTATISTICS_PATH . 'vendor/autoload.php';
}
require_once SEARCHSTATISTICS_PATH . 'Public.php';

// Start application
new SearchStatistics\App();

register_activation_hook(__FILE__, '\SearchStatistics\App::install');
