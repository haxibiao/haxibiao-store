<?php

namespace Haxibiao\Store\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallCommand extends Command
{

    /**
     * The name and signature of the Console command.
     *
     * @var string
     */
    protected $signature = 'store:install {--force}';

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
        $force = $this->option('force');

        $this->callSilent('migrate');

        $this->info('发布 store');
        $this->callSilent('vendor:publish', ['--provider' => 'Haxibiao\Store\StoreServiceProvider', '--force' => $force]);

        $this->comment('复制 stubs ...');
        copyStubs(__DIR__, $force);
    }

    protected function resolveStubPath($stub)
    {
        return __DIR__ . $stub;
    }
}
