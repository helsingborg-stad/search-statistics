<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use SearchStatistics\App;
use WpService\Implementations\NativeWpService;
use WpUtilService\WpUtilService;

final class AppTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testAdminStyleIsEnqueuedThroughNativeWpService(): void
    {
        function add_action($hook, $callback, $priority = 10, $acceptedArgs = 1): bool
        {
            $GLOBALS['testActions'][$hook][] = $callback;
            return true;
        }

        function plugins_url($path = '', $plugin = ''): string
        {
            $GLOBALS['testPluginPath'] = $plugin;
            return 'https://example.test/wp-content/plugins/search-statistics';
        }

        function wp_cache_get($key, $group = '', $force = false, &$found = null): array
        {
            return ['css/search-enhancer.css' => 'css/search-enhancer.abc.css'];
        }

        function wp_register_style($handle, $src, $deps = [], $ver = false, $media = 'all'): bool
        {
            $GLOBALS['testRegisteredStyle'] = [$handle, $src, $deps, $ver, $media];
            return true;
        }

        function wp_enqueue_style($handle, $src = '', $deps = [], $ver = false, $media = 'all'): void
        {
            $GLOBALS['testEnqueuedStyle'] = $handle;
        }

        $GLOBALS['wpdb'] = (object) ['base_prefix' => 'wp_'];
        $utility = new WpUtilService(new NativeWpService());
        $app = new App($utility->enqueue('/var/www/wp-content/plugins/search-statistics'));

        self::assertSame([$app, 'enqueueAdminStyle'], $GLOBALS['testActions']['admin_enqueue_scripts'][0]);
        ($GLOBALS['testActions']['admin_enqueue_scripts'][0])();

        self::assertSame('/var/www/wp-content/plugins/search-statistics//.', $GLOBALS['testPluginPath']);
        self::assertSame([
            'css-search-enhancercss',
            'https://example.test/wp-content/plugins/search-statistics/assets/dist/css/search-enhancer.abc.css',
            [],
            false,
            'all',
        ], $GLOBALS['testRegisteredStyle']);
        self::assertSame('css-search-enhancercss', $GLOBALS['testEnqueuedStyle']);
    }
}
