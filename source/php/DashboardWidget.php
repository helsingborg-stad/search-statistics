<?php

namespace SearchStatistics;

class DashboardWidget
{
    public function __construct()
    {
        add_action('wp_dashboard_setup', array($this, 'init'));
    }

    public function init()
    {
        wp_add_dashboard_widget(
            'search-enhancer-latest',
            __('Latest searches', 'search-enhancer'),
            array($this, 'latestSearches')
        );

        wp_add_dashboard_widget(
            'search-enhancer-unsuccessful',
            __('Unsuccessful searches', 'search-enhancer'),
            array($this, 'unsuccessfulSearches')
        );

        wp_add_dashboard_widget(
            'search-enhancer-popular',
            __('Popular searches', 'search-enhancer'),
            array($this, 'popularSearches')
        );
    }

    public function latestSearches()
    {
        $info = __('Shows the latest searches made and how many search results the search gave.', 'search-enhancer');

        $data = App::$logger->getLatest();
        $data = json_decode(json_encode($data));

        include SEARCHSTATISTICS_TEMPLATE_PATH . 'stats-table.php';
    }

    public function unsuccessfulSearches()
    {
        $info = __('Shows searches that gave no search results.', 'search-enhancer');

        $data = App::$logger->getUnsuccessful();
        $data = json_decode(json_encode($data));

        include SEARCHSTATISTICS_TEMPLATE_PATH . 'stats-table.php';
    }

    public function popularSearches()
    {
        $info = __('Shows the most popular searches and how many times the search phrase have been searched for.', 'search-enhancer');

        $data = App::$logger->getPopular();
        $data = json_decode(json_encode($data));

        include SEARCHSTATISTICS_TEMPLATE_PATH . 'stats-popular-table.php';
    }
}
