<?php

namespace SearchEnhancer;

class SearchLogger
{
    public function __construct()
    {
        add_action('template_redirect', array($this, 'log'));
    }

    /**
     * Log search query to the database
     * @return void
     */
    public function log()
    {
        global $wp_query;

        if (!$wp_query->is_search() || max(1, get_query_var('paged')) > 1) {
            return;
        }

        global $wpdb;

        $query = get_search_query();
        $hits = $wp_query->found_posts;
        $siteId = null;


        if (isset($_COOKIE['search_log']) && is_array(unserialize(stripslashes($_COOKIE['search_log']))) && in_array($query, unserialize(stripslashes($_COOKIE['search_log'])))) {
            return;
        }

        if (is_multisite() && function_exists('get_current_blog_id')) {
            $siteId = get_current_blog_id();
        }

        $insertedId = $wpdb->insert(
            \SearchEnhancer\App::$dbTable,
            array(
                'query' => $query,
                'results' => $hits,
                'site_id' => $siteId
            ),
            array(
                '%s',
                '%d',
                '%d'
            )
        );

        $cookie = array();

        if (is_array($_COOKIE['search_log'])) {
            $cookie = $_COOKIE['search_log'];
        }

        $cookie[] = $query;

        setcookie('search_log', serialize($cookie), time() + (86400 * 1), "/");
    }

    /**
     * Get latest logs from database
     * @param  integer $limit  How many rows?
     * @param  array   $siteId Which sites?
     * @param  array   $order  Which order?
     * @return array           Logs
     */
    public function getLatest($limit = 10, $siteId = array(), $order = array('date_searched', 'desc'))
    {
        global $wpdb;
        $table = \SearchEnhancer\App::$dbTable;

        $sql = "SELECT query, results, date_searched FROM " . $table;

        if (is_numeric($siteId)) {
            $sql .= " " . $wpdb->prepare("WHERE site_id = %d", $siteId);
        } elseif (is_array($siteId) && count($siteId) > 0) {
            $sql .= " WHERE site_id IN (" . implode(',', $siteId) . ")";
        }

        $sql .= " ORDER BY {$order[0]} " . strtoupper($order[1]);
        $sql .= " LIMIT " . $limit;

        return $wpdb->get_results($sql);
    }
}
