<?php
declare(strict_types=1);

namespace Kabunx\LaravelElasticsearch;

use Illuminate\Support\ServiceProvider;

class EsServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/elastic.php', 'elastic');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/elastic.php' => app()->configPath('elastic.php'),
            ], 'elastic-config');
        }
    }
}
