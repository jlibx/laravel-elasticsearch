<?php


namespace Golly\Elastic\Jobs;


use Golly\Elastic\ElasticEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class RemoveSearchable
 * @package Golly\Elastic\Jobs
 */
class RemoveSearchable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The models to be made searchable.
     *
     * @var Collection
     */
    public $models;

    /**
     * Create a new job instance.
     *
     * @param Collection $models
     * @return void
     */
    public function __construct(Collection $models)
    {
        $this->models = $models;
    }

    /**
     * @param ElasticEngine $engine
     * @return void
     */
    public function handle(ElasticEngine $engine)
    {
        $engine->delete($this->models);
    }
}
