<?php


namespace Golly\Elastic\Eloquent;

use Golly\Elastic\ElasticBuilder;
use Golly\Elastic\Jobs\MakeSearchable;
use Golly\Elastic\Jobs\RemoveSearchable;
use Golly\Elastic\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Trait HasElasticsearch
 * @package Golly\Elastic\Eloquent
 * @mixin Model
 */
trait HasElasticsearch
{

    /**
     * @var array
     */
    protected $searchMetaData = [];

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootHasElasticsearch()
    {
        static::observe(new ModelObserver());
    }


    /**
     * @return Builder
     */
    public static function elastic()
    {
        return (new static())->newElasticQuery();
    }

    /**
     * @return Builder
     */
    public function newElasticQuery()
    {
        return (new Builder(
            $this->newElasticBuilder()
        ))->setModel($this);
    }

    /**
     * @return ElasticBuilder
     */
    public function newElasticBuilder()
    {
        return new ElasticBuilder();
    }

    /**
     * @return array
     */
    public function getSearchMetadata()
    {
        return $this->searchMetaData;
    }

    /**
     * @param array $metaData
     */
    public function setSearchMetadata(array $metaData)
    {
        $this->searchMetaData = $metaData;
    }

    /**
     * @return bool
     */
    public function useSoftDelete()
    {
        return true;
    }

    /**
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
    public function getSearchIndex()
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
    public function getSearchKey()
    {
        return $this->getKey();
    }

    /**
     * Get the data array for the model.
     *
     * @return array
     */
    public function toSearchSource()
    {
        return $this->toArray();
    }

    /**
     * Before searchable
     *
     * @return void
     */
    public function beforeSearchable()
    {
    }

    /**
     * @return void
     */
    public function searchable()
    {
        $this->beforeSearchable();
        $models = $this->newCollection([$this]);
        $this->makeSearchable($models);
    }

    /**
     * @return void
     */
    public function unsearchable()
    {
        $models = $this->newCollection([$this]);
        $this->makeUnsearchable($models);
    }

    /**
     * @param EloquentBuilder $query
     * @return void
     */
    public function beforeAllSearchable(EloquentBuilder $query)
    {
    }

    /**
     * Make all instances of the model searchable.
     *
     * @param int|null $chunk
     * @return void
     */
    public function allSearchable(int $chunk = null)
    {
        $chunk = $chunk ?? config('elastic.chunk');
        $this->newQuery()->when(true, function ($query) {
            $this->beforeAllSearchable($query);
        })->orderBy(
            $this->getKeyName()
        )->chunk($chunk, function (Collection $models) {
            $this->makeSearchable($models);
        });
    }

    /**
     * @param int|null $chunk
     * @return void
     */
    public function removeAllSearchable(int $chunk = null)
    {
        $chunk = $chunk ?? config('elastic.chunk');
        $this->newQuery()->orderBy(
            $this->getKeyName()
        )->chunk($chunk, function (Collection $models) {
            $this->makeUnsearchable($models);
        });
    }

    /**
     * @param Collection $models
     * @return void
     */
    protected function makeSearchable(Collection $models)
    {
        if (config('elastic.queue')) {
            MakeSearchable::dispatch($models)->onQueue('elastic');
        } else {
            MakeSearchable::dispatchSync($models);
        }
    }


    /**
     * @param Collection $models
     * @return void
     */
    protected function makeUnsearchable(Collection $models)
    {
        if (config('elastic.queue')) {
            RemoveSearchable::dispatch($models)->onQueue('elastic');
        } else {
            RemoveSearchable::dispatchSync($models);
        }
    }

}
