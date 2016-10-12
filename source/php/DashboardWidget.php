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
        $data = App::$logger->getLatest();
        $data = json_decode(json_encode($data));

        include SEARCHSTATISTICS_TEMPLATE_PATH . 'stats-table.php';
    }

    public function unsuccessfulSearches()
    {
        $data = App::$logger->getUnsuccessful();
        $data = json_decode(json_encode($data));

        include SEARCHSTATISTICS_TEMPLATE_PATH . 'stats-table.php';
    }

    public function popularSearches()
    {
        $data = App::$logger->getPopular();
        $data = json_decode(json_encode($data));

        include SEARCHSTATISTICS_TEMPLATE_PATH . 'stats-popular-table.php';
    }
}
