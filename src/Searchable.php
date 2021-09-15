<?php
declare(strict_types=1);

namespace Kabunx\Elastic;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabunx\Elastic\Jobs\MakeSearchable;

/**
 * @mixin Model
 */
trait Searchable
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
    public static function bootSearchable()
    {
    }

    /**
     * Make all instances of the model searchable.
     *
     * @param int $chunk
     * @return void
     */
    public static function makeAllSearchable(int $chunk = 100)
    {
        $self = new static();
        $self->newQuery()
            ->when(true, function ($query) use ($self) {
                $query->with($self->getEsRelations());
            })
            ->when($self->useSoftDelete(), function ($query) {
                $query->withTrashed();
            })
            ->chunkById($chunk, function (Collection $models) use ($self) {
                $self->newEsBuilder()->update($models);
            }, $self->getEsIdName());
    }

    /**
     * @param int $chunk
     * @return void
     */
    public static function makeAllUnsearchable(int $chunk = 100)
    {
        $self = new static();
        $self->newQuery()
            ->select([$self->getEsIdName()])
            ->orderBy($self->getEsIdName())
            ->chunkById($chunk, function (Collection $models) use ($self) {
                $self->newEsBuilder()->delete($models);
            }, $self->getEsIdName());
    }

    /**
     * @return EsBuilder
     */
    public static function elasticsearch(): EsBuilder
    {
        return (new static())->newEsBuilder();
    }

    /**
     * @return EsBuilder
     */
    public static function elastic(): EsBuilder
    {
        return (new static())->newEsBuilder();
    }

    /**
     * @return EsBuilder
     */
    public function newEsBuilder(): EsBuilder
    {
        return (new EsBuilder())->setModel($this);
    }

    /**
     * @return array
     */
    public function getEsProperties(): array
    {
        return $this->newEsEntity()->getProperties();
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function getEsIndex(): string
    {
        $pieces = array_filter([
            config('elastic.prefix'),
            $this->getTable()
        ]);

        return implode('_', $pieces);
    }

    /**
     * @return string
     */
    public function getEsIdName(): string
    {
        return $this->getKeyName();
    }

    /**
     * Get the key name used to index the model.
     *
     * @return mixed
     */
    public function getEsId(): mixed
    {
        return $this->getKey();
    }

    /**
     * @return int
     */
    public function getEsPerPage(): int
    {
        return $this->getPerPage();
    }

    /**
     * 忽略检索
     *
     * @return bool
     */
    public function ignoreSearchable(): bool
    {
        return false;
    }

    /**
     * @return void
     */
    public function searchable(): void
    {
        $this->beforeSearchable();
        if (config('elastic.queue')) {
            dispatch(new MakeSearchable($this))->onQueue('elastic');
        } else {
            dispatch_sync(new MakeSearchable($this));
        }
    }

    /**
     * Get the data array for the model.
     *
     * @return array
     */
    public function toEsArray(): array
    {
        return array_merge($this->toArray(), $this->searchMetadata);
    }

    /**
     * Before searchable
     *
     * @return void
     */
    public function beforeSearchable(): void
    {
        $this->loadMissing($this->getEsRelations());
    }

    /**
     * @return array
     */
    public function getEsRelations(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getEsSoftDeletedColumn(): string
    {
        return 'is_soft_deleted';
    }

    /**
     * @return bool
     */
    public function getEsSoftDeletedValue(): bool
    {
        return true;
    }

    /**
     * Determine if the current class should use soft deletes with searching.
     *
     * @return bool
     */
    public function useSoftDelete(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this));
    }

    /**
     * @return bool
     */
    protected function isSoftDeleted(): bool
    {
        if (method_exists($this, 'trashed')) {
            return $this->trashed();
        }

        return false;
    }

    public function ifSoftDeletedAddMetadata(): void
    {
        if ($this->useSoftDelete()) {
            $this->addSearchMetadata(
                $this->getEsSoftDeletedColumn(),
                $this->isSoftDeleted()
            );
        }
    }

    /**
     * @return array
     */
    public function getSearchMetadata(): array
    {
        return $this->searchMetadata;
    }

    /**
     * @param array $searchMetadata
     */
    public function setSearchMetadata(array $searchMetadata): void
    {
        $this->searchMetadata = $searchMetadata;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addSearchMetadata(string $key, mixed $value): static
    {
        $this->searchMetadata[$key] = $value;

        return $this;
    }

}
