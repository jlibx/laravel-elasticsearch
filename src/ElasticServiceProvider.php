<?php
declare(strict_types=1);

namespace Golly\Elastic;

use Golly\Elastic\Console\ImportModel;
use Golly\Elastic\Console\RemoveModel;
use Illuminate\Support\ServiceProvider;

/**
 * Class ElasticServiceProvider
 * @package Golly\Elastic
 */
class ElasticServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        $this->publishConfigure();
    }

    /**
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportModel::class,
                RemoveModel::class
            ]);
        }
        $this->mergeConfigFrom(__DIR__ . '/../config/elastic.php', 'elastic');
    }

    /**
     * 发布配置文件
     *
     * @return void
     */
    protected function publishConfigure()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/elastic.php' => config_path('elastic.php'),
            ], 'elastic-config');
        }
    }
}
