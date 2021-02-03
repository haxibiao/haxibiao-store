<?php

namespace Haxibiao\Store;

use Haxibiao\Store\Console\InstallCommand;
use Haxibiao\Store\Console\PublishCommand;
use Illuminate\Support\ServiceProvider;

class StoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        // Register Commands
        $this->commands([
            InstallCommand::class,
            PublishCommand::class,
        ]);
        $this->bindPathsInContainer();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //注册 events - 运行时需要
        PlatformAccount::observe(Observers\PlatformAccountObserver::class);
        Refund::observe(Observers\RefundObserver::class);

        //加载 路由
        $this->loadRoutesFrom(
            __DIR__ . '/../router.php'
        );

        //安装时需要
        if ($this->app->runningInConsole()) {

            // 发布 graphql
            $this->publishes([
                __DIR__ . '/../graphql' => base_path('graphql'),
            ], 'store-graphql');

            //加载 migrations
            $this->loadMigrationsFrom($this->app->make('path.haxibiao-store.migrations'));
        }
    }

    /**
     * Bind paths in container.
     *
     * @return void
     */
    protected function bindPathsInContainer()
    {
        foreach ([
            'path.haxibiao-store'            => $root = dirname(__DIR__),
            'path.haxibiao-store.graphql'    => $root . '/graphql',
            'path.haxibiao-store.database'   => $database = $root . '/database',
            'path.haxibiao-store.migrations' => $database . '/migrations',
            'path.haxibiao-store.seeds'      => $database . '/seeds',
            'path.haxibiao-store.factories'  => $database . '/factories',
        ] as $abstract => $instance) {
            $this->app->instance($abstract, $instance);
        }
    }
}
