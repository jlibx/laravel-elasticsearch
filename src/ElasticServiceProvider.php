<?php


namespace Golly\Elastic;


use Golly\Elastic\Console\ModelFlush;
use Golly\Elastic\Console\ModelImport;
use Illuminate\Support\ServiceProvider;

/**
 * Class ElasticServiceProvider
 * @package Golly\Elastic
 */
class ElasticServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ModelImport::class,
                ModelFlush::class
            ]);
        }
    }
}
