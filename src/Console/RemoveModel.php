<?php


namespace Golly\Elastic\Console;


use Golly\Elastic\Eloquent\HasElasticsearch;
use Illuminate\Console\Command;

/**
 * Class RemoveModel
 * @package Golly\Elastic\Console
 */
class RemoveModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:remove {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Remove all of the model's records from the index";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        /** @var HasElasticsearch $class */
        $class = $this->argument('model');
        $class::makeAllUnsearchable();

        $this->info('All [' . $class . '] records have been removed.');
    }
}
