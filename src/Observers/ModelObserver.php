<?php
declare(strict_types=1);

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
    public function saved(Model $model): void
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
    public function deleted(Model $model): void
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
    public function forceDeleted(Model $model): void
    {
        $model->unsearchable();
    }

    /**
     * Handle the restored event for the model.
     *
     * @param Model|HasElasticsearch $model
     * @return void
     */
    public function restored(Model $model): void
    {
        $this->saved($model);
    }
}
