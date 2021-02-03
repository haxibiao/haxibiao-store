<?php

namespace Haxibiao\Store\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PublishCommand extends Command
{

    /**
     * The name and signature of the Console command.
     *
     * @var string
     */
    protected $signature = 'store:publish {--force}';

    /**
     * The Console command description.
     *
     * @var string
     */
    protected $description = '发布 haxibiao/store';

    /**
     * Execute the Console command.
     *
     * @return void
     */
    public function handle()
    {
        $force = $this->option('force');

        $this->comment("发布 store");
        $this->call('vendor:publish', ['--provider' => 'Haxibiao\Store\StoreServiceProvider', '--force' => $force]);
    }

}
