<?php


namespace Golly\Elastic\Jobs;


use Golly\Elastic\ElasticEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;

/**
 * Class MakeSearchable
 * @package Golly\Elastic\Jobs
 */
class MakeSearchable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesAndRestoresModelIdentifiers;

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
        $engine->update($this->models);
    }
}
