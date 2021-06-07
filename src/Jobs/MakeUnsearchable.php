<?php
declare(strict_types=1);

namespace Golly\Elastic\Jobs;

use Golly\Elastic\Engine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class MakeUnsearchable
 * @package Golly\Elastic\Jobs
 */
class MakeUnsearchable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * @param Engine $engine
     * @return void
     */
    public function handle(Engine $engine): void
    {
        $engine->delete(new Collection($this->model));
    }
}
