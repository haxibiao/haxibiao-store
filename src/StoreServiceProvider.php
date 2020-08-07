<?php

namespace Haxibiao\Store;

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
        PlatformAccount::observe(Observers\PlatformAccountObServer::class);
        Refund::observe(Observers\RefundObserver::class);

        //安装时需要
        if ($this->app->runningInConsole()) {

            // 发布 graphql
            $this->publishes([
                __DIR__ . '/../graphql' => base_path('graphql'),
            ], 'store-graphql');

            // 发布 tests
            $this->publishes([
                __DIR__ . '/../tests/Feature/GraphQL' => base_path('tests/Feature/GraphQL'),
            ], 'store-tests');

            // 发布 migrations
            $this->publishes([
                __DIR__ . '/../database/migrations' => base_path('database/migrations'),
            ], 'store-tests');

            // 发布 seed
            $this->publishes([
                __DIR__ . '/../database/seeds' => base_path('database/seeds'),
            ], 'store-tests');

            // 发布 factories
            $this->publishes([
                __DIR__ . '/../database/factories' => base_path('database/factories'),
            ], 'store-tests');

            // 发布 Nova
            $this->publishes([
                __DIR__ . '/Nova' => base_path('app/Nova'),
            ], 'store-nova');

            // // 发布 Factory
            // $this->publishes([
            //     __DIR__ . '/Factories' => base_path('app/Factories'),
            // ], 'store-nova');

            //注册 migrations paths
            $this->loadMigrationsFrom($this->app->make('path.haxibiao-store.migrations'));
        }
        //注册 migrations paths
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
