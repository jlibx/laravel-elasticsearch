<?php


namespace Golly\Elastic\Events;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelSearchable
 * @package Golly\Elastic\Events
 */
class ModelSearchable
{

    /**
     * @var Model
     */
    public $model;

    /**
     * ModelSearchable constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
