<?php

/**
 * Plugin Name:       Search Enhancer
 * Plugin URI:
 * Description:       Enhanced search with synonyms (if using ElasticPress) and statistics
 * Version:           1.0.0
 * Author:            Kristoffer Svanmark
 * Author URI:
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       search-enhancer
 * Domain Path:       /languages
 */

 // Protect agains direct file access
if (! defined('WPINC')) {
    die;
}

define('SEARCHENHANCER_PATH', plugin_dir_path(__FILE__));
define('SEARCHENHANCER_URL', plugins_url('', __FILE__));
define('SEARCHENHANCER_TEMPLATE_PATH', SEARCHENHANCER_PATH . 'templates/');

load_plugin_textdomain('search-enhancer', false, plugin_basename(dirname(__FILE__)) . '/languages');

require_once SEARCHENHANCER_PATH . 'source/php/Vendor/Psr4ClassLoader.php';
require_once SEARCHENHANCER_PATH . 'Public.php';

// Instantiate and register the autoloader
$loader = new SearchEnhancer\Vendor\Psr4ClassLoader();
$loader->addPrefix('SearchEnhancer', SEARCHENHANCER_PATH);
$loader->addPrefix('SearchEnhancer', SEARCHENHANCER_PATH . 'source/php/');
$loader->register();

// Start application
new SearchEnhancer\App();

register_activation_hook(__FILE__, '\SearchEnhancer\App::install');
