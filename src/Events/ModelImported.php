<?php


namespace Golly\Elastic\Events;


use Illuminate\Database\Eloquent\Collection;

/**
 * Class ModelImported
 * @package Golly\Elastic\Events
 */
class ModelImported
{

    public $models;

    public function __construct(Collection $models)
    {
        $this->models = $models;
    }
}
