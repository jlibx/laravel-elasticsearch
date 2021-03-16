<?php


namespace Golly\Elastic\Eloquent;

use Golly\Elastic\Engines\ElasticEngine;
use Golly\Elastic\Jobs\MakeSearchable;
use Golly\Elastic\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Trait Searchable
 * @package Golly\Elastic
 */
trait Searchable
{

    /**
     * @var array
     */
    protected $elasticMetadata = [];

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootSearchable()
    {
        static::addGlobalScope(new SearchableScope());

        static::observe(new ModelObserver());

        (new static())->registerSearchableMacros();
    }

    /**
     * Register the searchable macros.
     *
     * @return void
     */
    public function registerSearchableMacros()
    {
        $self = $this;
        Collection::macro('searchable', function () use ($self) {
            /**
             * @var Collection $this
             */
            $self->queueMakeSearchable($this);
        });

        Collection::macro('unsearchable', function () use ($self) {
            /**
             * @var Collection $this
             */
            $self->queueRemoveFromSearch($this);
        });
    }

    /**
     * Dispatch the job to make the given models searchable.
     *
     * @param Collection $models
     * @return void
     */
    public function queueMakeSearchable(Collection $models)
    {
        if ($models->isEmpty()) {
            return;
        }
        if (config('elastic.queue')) {
            dispatch(new MakeSearchable($models))->onQueue('elastic');
        } else {
            $this->getElasticEngine()->update($models);
        }
    }

    /**
     * Dispatch the job to make the given models unsearchable.
     *
     * @param Collection $models
     * @return void
     */
    public function queueRemoveFromSearch(Collection $models)
    {
        if ($models->isEmpty()) {
            return;
        }

        $this->getElasticEngine()->delete($models);
    }

    /**
     * Execute the search.
     *
     * @param Closure|null $callback
     * @return ElasticBuilder
     */
    public function elastic($callback = null)
    {
        return new ElasticBuilder($this, $callback);
    }

    /**
     * @param null $callback
     * @return ElasticBuilder
     */
    public static function newElastic($callback = null)
    {
        return (new static())->elastic($callback);
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return true;
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableIndex()
    {
        $pieces = [
            config('elastic.prefix'),
            $this->getTable()
        ];

        return implode('_', $pieces);
    }

    /**
     * Get the key name used to index the model.
     *
     * @return mixed
     */
    public function getSearchableKey()
    {
        return $this->getKey();
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return $this->toArray();
    }

    /**
     * @param Builder $query
     * @return void
     */
    public function beforeAllSearchable(Builder $query)
    {
    }

    /**
     * Make all instances of the model searchable.
     * SearchableScope 中已经绑定 searchable
     *
     * @param int $chunk
     * @return void
     */
    public function makeAllSearchable($chunk = null)
    {
        $this->newQuery()->when(true, function ($query) {
            $this->beforeAllSearchable($query);
        })->orderBy(
            $this->getKeyName()
        )->searchable($chunk);
    }


    /**
     * Make the given model instance searchable.
     * Collection 中已绑定 searchable
     *
     * @return void
     */
    public function searchable()
    {
        $this->newCollection([$this])->searchable();
    }

    /**
     * Remove all instances of the model from the search index.
     *
     * @return void
     */
    public function removeAllFromSearch()
    {
        $this->getElasticEngine()->flush($this);
    }

    /**
     * Remove the given model instance from the search index.
     * Collection 中已绑定 searchable
     *
     * @return void
     */
    public function unsearchable()
    {
        $this->newCollection([$this])->unsearchable();
    }

    /**
     * @return array
     */
    public function getElasticMetadata()
    {
        return $this->elasticMetadata;
    }

    /**
     * Set a Scout related metadata.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addElasticMetadata(string $key, $value)
    {
        $this->elasticMetadata[$key] = $value;

        return $this;
    }

    /**
     * Sync the soft deleted status for this model into the metadata.
     *
     * @return $this
     */
    public function pushSoftDeleteMetadata()
    {
        if ($this->useSoftDelete()) {
            $this->addElasticMetadata('__soft_deleted', $this->trashed() ? 1 : 0);
        }

        return $this;
    }

    /**
     * Determine if the current class should use soft deletes with searching.
     *
     * @return bool
     */
    public function useSoftDelete()
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this));
    }

    /**
     * @return ElasticEngine
     */
    protected function getElasticEngine()
    {
        return new ElasticEngine();
    }

}
