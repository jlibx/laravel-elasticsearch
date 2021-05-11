<?php


namespace Golly\Elastic\Eloquent;

use Golly\Elastic\ElasticBuilder;
use Golly\Elastic\Jobs\MakeSearchable;
use Golly\Elastic\Jobs\MakeUnsearchable;
use Golly\Elastic\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    protected $searchMetadata = [];

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
            $this->newElasticPrimaryQuery()
        ))->setModel($this);
    }

    /**
     * @return ElasticBuilder
     */
    public function newElasticPrimaryQuery()
    {
        return new ElasticBuilder();
    }

    /**
     * @return array
     */
    public function getSearchMetadata()
    {
        return $this->searchMetadata;
    }

    /**
     * @param array $metaData
     */
    public function setSearchMetadata(array $metaData)
    {
        $this->searchMetadata = $metaData;
    }

    /**
     * @param string $key
     * @param $data
     * @return $this
     */
    public function addSearchMetadata(string $key, $data)
    {
        $this->searchMetadata[$key] = $data;

        return $this;
    }

    /**
     * Determine if the current class should use soft deletes with searching.
     *
     * @return bool
     */
    public function useSoftDelete()
    {
        return in_array(SoftDeletes::class, class_uses_recursive(get_called_class()));
    }

    /**
     * @return void
     */
    public function pushSoftDeleteMetadata()
    {
        if ($this->useSoftDelete()) {
            /** @var static|SoftDeletes $this */
            $this->addSearchMetadata($this->getSoftDeletedColumn(), $this->trashed());
        }
    }

    /**
     * @return string
     */
    public function getSoftDeletedColumn()
    {
        return 'soft_deleted';
    }

    /**
     * @return mixed
     */
    public function getSoftDeletedValue()
    {
        return false;
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
     * @return array
     */
    public function getSearchRelations()
    {
        return [];
    }

    /**
     * Get the data array for the model.
     *
     * @return array
     */
    public function toSearchArray()
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
        $this->loadMissing($this->getSearchRelations());
    }

    /**
     * @return void
     */
    public function searchable()
    {
        $this->beforeSearchable();
        if (config('elastic.queue')) {
            MakeSearchable::dispatch($this)->onQueue('elastic');
        } else {
            MakeSearchable::dispatchSync($this);
        }
    }

    /**
     * @return void
     */
    public function unsearchable()
    {
        if (config('elastic.queue')) {
            MakeUnsearchable::dispatch($this)->onQueue('elastic');
        } else {
            MakeUnsearchable::dispatchSync($this);
        }
    }

    /**
     * @param EloquentBuilder $query
     * @return void
     */
    public function beforeAllSearchable(EloquentBuilder $query)
    {
        $query->with($this->getSearchRelations());
    }

    /**
     * Make all instances of the model searchable.
     *
     * @param int|null $chunk
     * @return void
     */
    public static function makeAllSearchable(int $chunk = null)
    {
        $self = new static();
        $chunk = $chunk ?? config('elastic.chunk');
        $softDelete = $self->useSoftDelete();
        $self->newQuery()->when(true, function ($query) use ($self) {
            $self->beforeAllSearchable($query);
        })->when($softDelete, function ($query) {
            $query->withTrashed();
        })->chunkById($chunk, function ($models) use ($self) {
            $self->newElasticPrimaryQuery()->update($models);
        });
    }

    /**
     * @param int|null $chunk
     * @return void
     */
    public static function makeAllUnsearchable(int $chunk = null)
    {
        $self = new static();
        $chunk = $chunk ?? config('elastic.chunk');
        $self->newQuery()->orderBy(
            $self->getKeyName()
        )->chunkById($chunk, function (Collection $models) use ($self) {
            $self->newElasticPrimaryQuery()->delete($models);
        });
    }
}
