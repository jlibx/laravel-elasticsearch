<?php


namespace Golly\Elastic\Jobs;


use Golly\Elastic\Engines\ElasticEngine;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MakeSearchable
 * @package Golly\Elastic\Jobs
 */
class MakeSearchable
{
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
     * Handle the job.
     *
     * @return void
     */
    public function handle()
    {
        if (count($this->models) === 0) {
            return;
        }

        (new ElasticEngine())->update($this->models);
    }
}
