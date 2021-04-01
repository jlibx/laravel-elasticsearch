<?php


namespace Golly\Elastic\Console;


use Golly\Elastic\Eloquent\HasElasticsearch;
use Golly\Elastic\Events\ModelSearchable;
use Illuminate\Console\Command;
use Illuminate\Events\Dispatcher;

/**
 * Class ImportModel
 * @package Golly\Elastic\Console
 */
class ImportModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:import
            {model : Class name of model to bulk import}
            {--c|chunk= : The number of records to import at a time (Defaults to configuration value: `elastic.chunk`)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the given model into the search index';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        /** @var HasElasticsearch $class */
        $class = $this->argument('model');
        $class::makeAllSearchable($this->option('chunk'));

        $this->info('All [' . $class . '] records have been imported.');
    }
}
