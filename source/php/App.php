<?php

namespace SearchEnhancer;

class App
{
    public static $wpdb = null;
    public static $dbTable = null;

    public function __construct()
    {
        global $wpdb;
        self::$wpdb = $wpdb;

        self::$dbTable = $wpdb->base_prefix . 'se_search_log';

        add_action('admin_init', '\SearchEnhancer\App::install');
    }

    public static function install()
    {
        update_option('search-enhancer-db-version', 0);
        $charsetCollation = self::$wpdb->get_charset_collate();
        $tableName = self::$dbTable;

        if (!empty(get_site_option('search-enhancer-db-version')) && self::$wpdb->get_var("SHOW TABLES LIKE '$tableName'") == $tableName) {
            return;
        }

        $sql = "CREATE TABLE $tableName (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            query varchar(255) DEFAULT NULL,
            results bigint(20) DEFAULT 0 NOT NULL,
            site_id bigint(20) DEFAULT NULL,
            date_searched timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
            UNIQUE KEY id (id)
        ) $charsetCollation;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_option('search-enhancer-db-version', 1);
    }
}