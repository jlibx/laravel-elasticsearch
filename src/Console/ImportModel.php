<?php
declare(strict_types=1);

namespace Golly\Elastic\Console;


use Golly\Elastic\Eloquent\HasElasticsearch;
use Illuminate\Console\Command;

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
     * @return int
     */
    public function handle(): int
    {
        /** @var HasElasticsearch $class */
        $class = $this->argument('model');
        $class::makeAllSearchable($this->option('chunk'));

        $this->info('All [' . $class . '] records have been imported.');

        return 0;
    }
}
