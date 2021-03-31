<?php


namespace Golly\Elastic\Observers;


use Golly\Elastic\Eloquent\HasElasticsearch;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelObserver
 * @package Golly\Elastic\Observers
 */
class ModelObserver
{
    /**
     * Handle the saved event for the model.
     *
     * @param Model|HasElasticsearch $model
     * @return void
     */
    public function saved($model)
    {
        if (!$model->shouldBeSearchable()) {
            $model->unsearchable();

            return;
        }

        $model->searchable();
    }

    /**
     * Handle the deleted event for the model.
     *
     * @param Model|HasElasticsearch $model
     * @return void
     */
    public function deleted($model)
    {
        if ($model->useSoftDelete()) {
            $this->saved($model);
        } else {
            $model->unsearchable();
        }
    }

    /**
     * Handle the force deleted event for the model.
     *
     * @param Model|HasElasticsearch $model
     * @return void
     */
    public function forceDeleted($model)
    {
        $model->unsearchable();
    }

    /**
     * Handle the restored event for the model.
     *
     * @param Model|HasElasticsearch $model
     * @return void
     */
    public function restored($model)
    {
        $this->saved($model);
    }
}
