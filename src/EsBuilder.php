<?php
declare(strict_types=1);

namespace Kabunx\LaravelElasticsearch;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\ForwardsCalls;
use Kabunx\LaravelElasticsearch\Contracts\SearchableInterface;
use Kabunx\Elasticsearch\Builder;


/**
 * @mixin Builder
 */
class EsBuilder
{
    use ForwardsCalls;

    protected Builder $builder;

    /**
     * @var SearchableInterface
     */
    protected SearchableInterface $model;

    /**
     * 是否包含软删数据
     *
     * @var bool
     */
    protected bool $withSoftDeleted = false;


    public function __construct()
    {
        $this->initBuilder();
    }

    protected function initBuilder()
    {
        $hosts = config('elastic.hosts', []);
        $this->builder = (new Builder())->setHosts($hosts);
    }

    /**
     * @return $this
     */
    public function withTrashed(): static
    {
        $this->withSoftDeleted = true;

        return $this;
    }

    /**
     * @param array $columns
     * @return array
     */
    public function raw(array $columns = []): array
    {
        if (! $this->withSoftDeleted && $this->model->isUseSoftDeletes()) {
            $this->builder->term(
                $this->model->getEsSoftDeletedColumn(),
                $this->model->getEsNotSoftDeletedValue()
            );
        }
        if (config('elastic.log')) {
            Log::info('es params ' . json_encode($this->builder->toSearchParams()));
        }


        return $this->builder->select($columns)->get();
    }

    /**
     * @param array $columns
     * @return EsCollection
     */
    public function get(array $columns = []): EsCollection
    {
        $data = $this->raw($columns);

        return EsCollection::make($this->model->newEsEntity(), $data);
    }

    /**
     * @param int|null $perPage
     * @param array $columns
     * @param string $pageName
     * @param int|null $page
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = null, array $columns = [], string $pageName = 'page', int $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?: $this->model->getEsPerPage();
        $page = $page ?: Paginator::resolveCurrentPage($pageName);
        $offset = ($page - 1) * $perPage;
        $this->builder->offset($offset)->limit($perPage);

        return $this->get($columns)->toPaginator($perPage, $page);
    }

    /**
     * @param bool $dynamic
     * @return array
     */
    public function autoCreateIndex(bool $dynamic = true): array
    {
        return $this->builder->createIndex(
            $this->model->getEsProperties(), $dynamic
        );
    }

    /**
     * @param array $properties
     * @param bool $dynamic
     * @return array
     */
    public function createIndex(array $properties = [], bool $dynamic = true): array
    {
        return $this->builder->createIndex($properties, $dynamic);
    }

    /**
     * @return array
     */
    public function deleteIndex(): array
    {
        return $this->builder->deleteIndex();
    }

    /**
     * @param Collection $models
     * @return array
     */
    public function update(Collection $models): array
    {
        $data = [];
        foreach ($models as $model) {
            if ($model instanceof SearchableInterface) {
                $model->addMetadataIfSoftDeleted();
                $data[] = [
                    'id' => $model->getEsId(),
                    'doc' => $model->toEsArray()
                ];
            }
        }

        return $this->builder->update($data);
    }

    /**
     * @param Collection $models
     * @return array
     */
    public function delete(Collection $models): array
    {
        $ids = [];
        foreach ($models as $model) {
            if ($model instanceof SearchableInterface) {
                $ids[] = $model->getEsId();
            }
        }

        return $this->builder->delete($ids);
    }

    /**
     * 将指定索引
     *
     * @param SearchableInterface $model
     * @return $this
     */
    public function setModel(SearchableInterface $model): static
    {
        $this->model = $model;
        $this->builder->setIndex($model->getEsIndex());

        return $this;
    }

    public function __call(string $name, array $arguments)
    {
        $result = $this->forwardCallTo($this->builder, $name, $arguments);

        return $result instanceof Builder ? $this : $result;
    }
}
