<?php

declare(strict_types=1);

namespace Golly\Elastic\Jobs;

use Golly\Elastic\ElasticEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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
     * @var Model
     */
    public Model $model;

    /**
     * Create a new job instance.
     *
     * @param Model $model
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param ElasticEngine $engine
     * @return void
     */
    public function handle(ElasticEngine $engine): void
    {
        $engine->update(new Collection($this->model));
    }
}
