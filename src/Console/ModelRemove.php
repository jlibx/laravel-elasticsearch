<?php


namespace Golly\Elastic\Console;


use Illuminate\Console\Command;

/**
 * Class ModelRemove
 * @package Golly\Elastic\Console
 */
class ModelRemove extends Command
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
        $class = $this->argument('model');

        (new $class)->removeAllSearchable();

        $this->info('All [' . $class . '] records have been removed.');
    }
}
