<?php
declare(strict_types=1);

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
     * @return int
     */
    public function handle(): int
    {
        /** @var HasElasticsearch $class */
        $class = $this->argument('model');
        $class::makeAllUnsearchable();

        $this->info('All [' . $class . '] records have been removed.');

        return 0;
    }
}
