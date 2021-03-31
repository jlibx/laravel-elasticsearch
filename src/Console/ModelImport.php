<?php


namespace Golly\Elastic\Console;


use Golly\Elastic\Eloquent\HasElasticsearch;
use Golly\Elastic\Events\ModelImported;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;

/**
 * Class ModelImport
 * @package Golly\Elastic\Console
 */
class ModelImport extends Command
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
     * @param Dispatcher $events
     * @return void
     */
    public function handle(Dispatcher $events)
    {
        $class = $this->argument('model');
        $events->listen(ModelImported::class, function ($event) use ($class) {
            /**
             * @var Model|HasElasticsearch $model
             */
            $model = $event->models->last();
            $this->comment('Imported [' . $class . '] models up to ID: ' . $model->getSearchKey());
        });
        (new $class)->allSearchable($this->option('chunk'));

        $events->forget(ModelImported::class);

        $this->info('All [' . $class . '] records have been imported.');
    }
}
