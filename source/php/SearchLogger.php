<?php

namespace SearchEnhancer;

class SearchLogger
{
    public function __construct()
    {
        add_action('template_redirect', array($this, 'log'));
    }

    public function log()
    {
        global $wp_query;

        if (!$wp_query->is_search()) {
            return;
        }

        global $wpdb;

        $query = get_search_query();
        $hits = $wp_query->found_posts;
        $siteId = null;

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
    }
}
