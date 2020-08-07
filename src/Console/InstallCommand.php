<?php

namespace Haxibiao\Store;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallCommand extends Command
{

    /**
     * The name and signature of the Console command.
     *
     * @var string
     */
    protected $signature = 'store:install';

    /**
     * The Console command description.
     *
     * @var string
     */
    protected $description = '安装 haxibiao/store';

    /**
     * Execute the Console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('发布 store');
        $this->call('vendor:publish', ['--provider' => 'Haxibiao\Store\StoreServiceProvider', '--force']);

        $this->comment('复制 stubs ...');
        copy($this->resolveStubPath('/stubs/Store.stub'), app_path('Store.php'));
        copy($this->resolveStubPath('/stubs/Refund.stub'), app_path('Refund.php'));
        copy($this->resolveStubPath('/stubs/Item.stub'), app_path('Item.php'));
        copy($this->resolveStubPath('/stubs/Order.stub'), app_path('Order.php'));
        copy($this->resolveStubPath('/stubs/PlatformAccount.stub'), app_path('PlatformAccount.php'));
        copy($this->resolveStubPath('/stubs/Product.stub'), app_path('Product.php'));
        copy($this->resolveStubPath('/stubs/ExchangeConfig.stub'), app_path('ExchangeConfig.php'));
    }

    protected function resolveStubPath($stub)
    {
        return __DIR__ . $stub;
    }
}
