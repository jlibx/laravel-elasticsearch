<?php


namespace Golly\Elastic\Eloquent;

use Golly\Elastic\Builder as EsBuilder;
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
    protected array $searchMetadata = [];

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
    public static function elastic(): Builder
    {
        return (new static())->newEloquentEsBuilder();
    }

    /**
     * @return Builder
     */
    public function newEloquentEsBuilder(): Builder
    {
        return (new Builder(
            $this->newEsBuilder()
        ))->setModel($this);
    }

    /**
     * @return EsBuilder
     */
    public function newEsBuilder(): EsBuilder
    {
        return new EsBuilder();
    }

    /**
     * @return array
     */
    public function getSearchMetadata(): array
    {
        return $this->searchMetadata;
    }

    /**
     * @param array $metaData
     * @return $this
     */
    public function setSearchMetadata(array $metaData): static
    {
        $this->searchMetadata = $metaData;

        return $this;
    }

    /**
     * @param string $key
     * @param $data
     * @return $this
     */
    public function addSearchMetadata(string $key, $data): static
    {
        $this->searchMetadata[$key] = $data;

        return $this;
    }

    /**
     * Determine if the current class should use soft deletes with searching.
     *
     * @return bool
     */
    public function useSoftDelete(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive(get_called_class()));
    }

    /**
     * @return void
     */
    public function prepareSoftDeletedMetadata(): void
    {
        if ($this->useSoftDelete()) {
            /** @var static|SoftDeletes $this */
            $this->addSearchMetadata($this->getSoftDeletedColumn(), $this->trashed());
        }
    }

    /**
     * @return string
     */
    public function getSoftDeletedColumn(): string
    {
        return 'soft_deleted';
    }

    /**
     * @return bool
     */
    public function getSoftDeletedValue(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function shouldBeSearchable(): bool
    {
        return true;
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function getSearchIndex(): string
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
    public function getSearchKey(): mixed
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getSearchRelations(): array
    {
        return [];
    }

    /**
     * Get the data array for the model.
     *
     * @return array
     */
    public function toSearchArray(): array
    {
        return $this->toArray();
    }


    /**
     * Before searchable
     *
     * @return void
     */
    public function beforeSearchable(): void
    {
        $this->loadMissing($this->getSearchRelations());
    }

    /**
     * @return void
     */
    public function searchable(): void
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
    public function unsearchable(): void
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
    public function beforeAllSearchable(EloquentBuilder $query): void
    {
        $query->with($this->getSearchRelations());
    }

    /**
     * Make all instances of the model searchable.
     *
     * @param int|null $chunk
     * @return void
     */
    public static function makeAllSearchable(int $chunk = null): void
    {
        $self = new static();
        $chunk = $chunk ?? config('elastic.chunk');
        $softDelete = $self->useSoftDelete();
        $self->newQuery()->when(true, function ($query) use ($self) {
            $self->beforeAllSearchable($query);
        })->when($softDelete, function ($query) {
            $query->withTrashed();
        })->chunkById($chunk, function ($models) use ($self) {
            $self->newEsBuilder()->update($models);
        });
    }

    /**
     * @param int|null $chunk
     * @return void
     */
    public static function makeAllUnsearchable(int $chunk = null): void
    {
        $self = new static();
        $chunk = $chunk ?? config('elastic.chunk');
        $self->newQuery()->orderBy(
            $self->getKeyName()
        )->chunkById($chunk, function (Collection $models) use ($self) {
            $self->newEsBuilder()->delete($models);
        });
    }
}
