<?php
declare(strict_types=1);

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
    public Model $model;

    /**
     * ModelSearchable constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
