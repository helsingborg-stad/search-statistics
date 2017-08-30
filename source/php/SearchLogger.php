<?php

namespace SearchStatistics;

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
        $loggedIn = is_user_logged_in();


        if (isset($_COOKIE['search_log']) && is_array(unserialize(stripslashes($_COOKIE['search_log']))) && in_array($query, unserialize(stripslashes($_COOKIE['search_log'])))) {
            return;
        }

        if (is_multisite() && function_exists('get_current_blog_id')) {
            $siteId = get_current_blog_id();
        }

        $insertedId = $wpdb->insert(
            \SearchStatistics\App::$dbTable,
            array(
                'query' => $query,
                'results' => $hits,
                'site_id' => $siteId,
                'logged_in' => $loggedIn
            ),
            array(
                '%s',
                '%d',
                '%d',
                '%d'
            )
        );

        $cookie = array();

        if (isset($_COOKIE['search_log']) && is_array($_COOKIE['search_log'])) {
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
        $table = \SearchStatistics\App::$dbTable;

        $sql = "SELECT query, results, date_searched, logged_in FROM " . $table;

        $sql .= " WHERE query != '' AND results > 0";

        if (LOCAL_SITE_STATS === true) {
            $sql .= " AND site_id = '" . get_current_blog_id() . "'";
        } elseif (is_numeric($siteId)) {
            $sql .= " " . $wpdb->prepare("site_id = %d", $siteId);
        } elseif (is_array($siteId) && count($siteId) > 0) {
            $sql .= " site_id IN (" . implode(',', $siteId) . ")";
        }

        $sql .= " ORDER BY {$order[0]} " . strtoupper($order[1]);
        $sql .= " LIMIT " . $limit;

        return $wpdb->get_results($sql);
    }

    public function getUnsuccessful($limit = 10, $siteId = array(), $order = array('date_searched', 'desc'))
    {
        global $wpdb;
        $table = \SearchStatistics\App::$dbTable;

        $sql = "SELECT query, results, date_searched, logged_in FROM " . $table;

        $sql .= " WHERE query != ''";

        if (LOCAL_SITE_STATS === true) {
            $sql .= " AND site_id = '" . get_current_blog_id() . "'";
        } elseif (is_numeric($siteId)) {
            $sql .= " " . $wpdb->prepare("site_id = %d", $siteId);
        } elseif (is_array($siteId) && count($siteId) > 0) {
            $sql .= " site_id IN (" . implode(',', $siteId) . ")";
        }

        $sql .= " AND results = 0";

        $sql .= " ORDER BY {$order[0]} " . strtoupper($order[1]);
        $sql .= " LIMIT " . $limit;

        return $wpdb->get_results($sql);
    }

    public function getPopular($limit = 10, $siteId = array())
    {
        global $wpdb;
        $table = \SearchStatistics\App::$dbTable;

        $sql = "SELECT query, results, date_searched, logged_in, count(id) AS num_searches FROM " . $table;
        $sql .= " WHERE query != '' AND results > 0";
        $sql .= " AND date_searched >= DATE(NOW()) - INTERVAL 7 DAY";

        if (LOCAL_SITE_STATS === true) {
            $sql .= " AND site_id = '" . get_current_blog_id() . "'";
        } elseif (is_numeric($siteId)) {
            $sql .= " " . $wpdb->prepare("site_id = %d", $siteId);
        } elseif (is_array($siteId) && count($siteId) > 0) {
            $sql .= " site_id IN (" . implode(',', $siteId) . ")";
        }

        $sql .= " GROUP BY query";
        $sql .= " ORDER BY num_searches DESC";
        $sql .= " LIMIT " . $limit;

        return $wpdb->get_results($sql);
    }
}
