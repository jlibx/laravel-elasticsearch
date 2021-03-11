<?php


namespace Golly\Elastic\Events;

use Illuminate\Database\Eloquent\Collection;

/**
 * Class ModelFlushed
 * @package Golly\Elastic\Events
 */
class ModelFlushed
{

    /**
     * @var Collection
     */
    public $models;

    /**
     * ModelFlushed constructor.
     * @param Collection $models
     */
    public function __construct(Collection $models)
    {
        $this->models = $models;
    }
}
