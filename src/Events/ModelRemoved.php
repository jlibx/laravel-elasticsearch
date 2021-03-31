<?php


namespace Golly\Elastic\Events;


use Illuminate\Database\Eloquent\Collection;

class ModelRemoved
{

    public $models;

    /**
     * ModelRemoved constructor.
     * @param Collection $models
     */
    public function __construct(Collection $models)
    {
        $this->models = $models;
    }
}
