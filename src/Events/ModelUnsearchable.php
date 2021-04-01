<?php


namespace Golly\Elastic\Events;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelUnsearchable
 * @package Golly\Elastic\Events
 */
class ModelUnsearchable
{

    /**
     * @var Model
     */
    public $model;

    /**
     * ModelRemoved constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
