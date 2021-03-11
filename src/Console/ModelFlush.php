<?php


namespace Golly\Elastic\Console;


use Illuminate\Console\Command;

/**
 * Class ModelFlush
 * @package Golly\Elastic\Console
 */
class ModelFlush extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:flush {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Flush all of the model's records from the index";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $class = $this->argument('model');

        (new $class)->removeAllFromSearch();

        $this->info('All [' . $class . '] records have been flushed.');
    }

}
