<?php


namespace Golly\Elastic\Eloquent;


use Golly\Elastic\Events\ModelFlushed;
use Golly\Elastic\Events\ModelImported;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Scope;

/**
 * Class SearchableScope
 * @package Golly\Elastic
 */
class SearchableScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        // TODO: Implement apply() method.
    }

    /**
     * 全局扩展
     *
     * @param Builder $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        $builder->macro('searchable', function (Builder $builder, $chunk = null) {
            $builder->chunkById($chunk ?: config('elastic.chunk', 500), function ($models) {
                // Collection 已绑定 searchable 函数
                $models->searchable();

                event(new ModelImported($models));
            });
        });
        $builder->macro('unsearchable', function (Builder $builder, $chunk = null) {
            $builder->chunkById($chunk ?: config('elastic.chunk', 500), function ($models) {
                $models->unsearchable();

                event(new ModelFlushed($models));
            });
        });

        HasManyThrough::macro('searchable', function ($chunk = null) {
            /**
             * @var HasManyThrough $this
             */
            $this->chunkById($chunk ?: config('elastic.chunk', 500), function ($models) {
                $models->searchable();

                event(new ModelImported($models));
            });
        });

        HasManyThrough::macro('unsearchable', function ($chunk = null) {
            /**
             * @var HasManyThrough $this
             */
            $this->chunkById($chunk ?: config('elastic.chunk', 500), function ($models) {
                $models->unsearchable();

                event(new ModelFlushed($models));
            });
        });

    }

}
