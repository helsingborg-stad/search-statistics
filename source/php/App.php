<?php

namespace SearchStatistics;

class App
{
    public static $wpdb = null;
    public static $dbTable = null;
    public static $logger = null;

    public function __construct()
    {
        global $wpdb;
        self::$wpdb = $wpdb;
        self::$dbTable = $wpdb->base_prefix . 'search_statistics_log';

        add_action('admin_enqueue_scripts', array($this, 'enqueueAdminStyle'));
        add_action('admin_init', array($this, 'updateTables'));

        self::$logger = new \SearchStatistics\SearchLogger();
        new \SearchStatistics\DashboardWidget();
    }

    /**
     * Creates the search log db table
     * @return void
     */
    public static function install()
    {
        update_option('search-statistics-db-version', 0);
        $charsetCollation = self::$wpdb->get_charset_collate();
        $tableName = self::$dbTable;

        if (!empty(get_site_option('search-statistics-db-version')) && self::$wpdb->get_var("SHOW TABLES LIKE '$tableName'") == $tableName) {
            return;
        }

        $sql = "CREATE TABLE $tableName (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            query varchar(255) DEFAULT NULL,
            results bigint(20) DEFAULT 0 NOT NULL,
            site_id bigint(20) DEFAULT NULL,
            logged_in tinyint(1) DEFAULT 0 NOT NULL,
            date_searched timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
            UNIQUE KEY id (id)
        ) $charsetCollation;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_option('search-statistics-db-version', 2);
    }

    public function updateTables()
    {
        global $wpdb;

        if (get_site_option('search-statistics-db-version') < 2) {
            $tableName = self::$dbTable;
            $wpdb->query("ALTER TABLE $tableName ADD logged_in tinyint(1) DEFAULT 0 NOT NULL");

            update_option('search-statistics-db-version', 2);
        }
    }

    public function enqueueAdminStyle()
    {
        wp_enqueue_style('search-enhancer', SEARCHSTATISTICS_URL . '/dist/css/search-enhancer.min.css', null, '1.0.0');
    }
}
