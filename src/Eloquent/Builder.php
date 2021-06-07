<?php
declare(strict_types=1);

namespace Golly\Elastic\Eloquent;

use Closure;
use Golly\Elastic\Builder as EsBuilder;
use Golly\Elastic\Hydrate\ElasticEntity;
use Golly\Elastic\Exceptions\ElasticException;
use Golly\Elastic\Queries\Compound\BoolQuery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * Class Builder
 * @package Golly\Elastic\Eloquent
 * @mixin Builder
 */
class Builder
{
    use ForwardsCalls;

    /**
     * @var EsBuilder
     */
    protected EsBuilder $esBuilder;

    /**
     * @var Model|HasElasticsearch
     */
    protected Model $model;

    /**
     * @var array
     */
    protected array $relations = [];

    /**
     * @var array
     */
    protected array $countRelations = [];

    /**
     * @var string[]
     */
    protected array $interruptions = [
        'toSearchParams'
    ];

    /**
     * Builder constructor.
     * @param EsBuilder $esBuilder
     */
    public function __construct(EsBuilder $esBuilder)
    {
        $this->esBuilder = $esBuilder;
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @return $this
     * @throws ElasticException
     */
    public function must($column, $operator = null, $value = null): self
    {
        return $this->where($column, $operator, $value);
    }

    /**
     * @param string $column
     * @param string $value
     * @return $this
     * @throws ElasticException
     */
    public function mustLike(string $column, string $value): self
    {
        return $this->must($column, 'like', $value);
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @param string $type
     * @return $this
     * @throws ElasticException
     */
    public function where($column, $operator = null, $value = null, string $type = 'must'): self
    {
        if ($column instanceof Closure && is_null($operator)) {
            $column($query = $this->model->newEloquentEsBuilder());

            $this->esBuilder->addToBoolWhereQuery($query->getEsBuilder(), $type);
        } else {
            $this->esBuilder->where(...func_get_args());
        }

        return $this;
    }

    /**
     * @param callable $callback
     * @param string $type
     * @return $this
     */
    public function whereBool(callable $callback, string $type = 'must'): self
    {
        $callback($query = $this->model->newEloquentEsBuilder());

        $this->esBuilder->addToBoolWhereQuery($query->getEsBuilder(), $type);

        return $this;
    }

    /**
     * @param string $column
     * @param mixed $value
     * @return $this
     * @throws ElasticException
     */
    public function whereEqual(string $column, mixed $value): self
    {
        return $this->where($column, '=', $value);
    }

    /**
     * @param string $column
     * @param string $value
     * @return $this
     * @throws ElasticException
     */
    public function whereLike(string $column, string $value): self
    {
        return $this->where($column, 'like', $value);
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return $this
     * @throws ElasticException
     */
    public function should($column, $operator = null, $value = null)
    {
        return $this->orWhere($column, $operator, $value);
    }

    /**
     * @param string $column
     * @param string $value
     * @return $this
     * @throws ElasticException
     */
    public function shouldLike(string $column, string $value)
    {
        return $this->should($column, 'like', $value);
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return $this
     * @throws ElasticException
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        [$value, $operator] = $this->esBuilder->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->where($column, $operator, $value, BoolQuery::SHOULD);
    }

    /**
     * @param string $column
     * @param string $value
     * @return $this
     * @throws ElasticException
     */
    public function orWhereLike(string $column, string $value)
    {
        return $this->orWhere($column, 'like', $value);
    }

    /**
     * @param string $relation
     * @param Closure $callback
     * @return $this
     */
    public function whereHas(string $relation, Closure $callback)
    {
        $builder = $this->model->newEloquentEsBuilder();
        $builder->setRelation($relation);
        $callback($builder);
        $tWheres = $builder->getEsBuilder()->getBoolQueryWheres();
        foreach ($tWheres as $type => $wheres) {
            foreach ($wheres as $where) {
                $this->esBuilder->addToBoolQuery($where, $type);
            }
        }

        return $this;
    }

    /**
     * @param array $relations
     * @return $this
     */
    public function with(array $relations)
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * @param array $relations
     * @return $this
     */
    public function withCount(array $relations)
    {
        $this->countRelations = $relations;

        return $this;
    }

    /**
     * @param array $columns
     * @param array $options
     * @return ElasticEntity
     * @throws ElasticException
     */
    public function raw(array $columns = [], array $options = [])
    {
        if ($this->model->useSoftDelete()) {
            $this->esBuilder->where(
                $this->model->getSoftDeletedColumn(),
                '!=',
                $this->model->getSoftDeletedValue()
            );
        }

        return $this->esBuilder->select($columns)->get($options);
    }

    /**
     * @param array $columns
     * @param array $options
     * @return Collection
     * @throws ElasticException
     */
    public function get(array $columns = [], array $options = [])
    {
        $entity = $this->raw($columns, $options);

        return $this->toCollection($entity);
    }

    /**
     * @param array $columns
     * @param array $options
     * @return Model|null
     * @throws ElasticException
     */
    public function first(array $columns = [], array $options = [])
    {
        $this->esBuilder->limit(1);

        return $this->get($columns, $options)->first();
    }

    /**
     * @param array $columns
     * @param array $options
     * @return ElasticEntity
     * @throws ElasticException
     */
    public function rawFirst(array $columns = [], array $options = [])
    {
        $this->esBuilder->limit(1);

        return $this->raw($columns, $options);
    }

    /**
     * @param null $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     * @throws ElasticException
     */
    public function paginate($perPage = null, $columns = [], $pageName = 'page', $page = null)
    {
        [$page, $prePage] = $this->prepareCurrentPage($perPage, $pageName, $page);
        $offset = ($page - 1) * $prePage;
        $this->esBuilder->offset($offset)->limit($prePage);
        $entity = $this->raw($columns);
        $collection = $this->toCollection($entity);

        return $entity->paginate($prePage, $page, $collection);
    }

    /**
     * @param null $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     * @throws ElasticException
     */
    public function rawPaginate($perPage = null, $columns = [], $pageName = 'page', $page = null)
    {
        [$page, $prePage] = $this->prepareCurrentPage($perPage, $pageName, $page);
        $offset = ($page - 1) * $prePage;
        $this->esBuilder->offset($offset)->limit($prePage);
        $entity = $this->raw($columns);

        return $entity->paginate($prePage, $page);
    }

    /**
     * @return void
     */
    public function dd()
    {
        $this->esBuilder->dd();
    }

    /**
     * @return Builder
     */
    public function getEsBuilder(): Builder
    {
        return $this->esBuilder;
    }

    /**
     * @return Model|HasElasticsearch
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Model|HasElasticsearch $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;
        $this->esBuilder->from($model->getSearchIndex());

        return $this;
    }

    /**
     * @param ElasticEntity $entity
     * @return Collection
     */
    protected function toCollection(ElasticEntity $entity)
    {
        $ids = $entity->getIds();
        $fields = implode(',', $ids);
        $collection = $this->model->newQuery()->orderByRaw(
            DB::raw("field(id,{$fields})")
        )->findMany($ids);

        if ($this->relations) {
            $collection->loadMissing($this->relations);
        }
        if ($this->countRelations) {
            $collection->loadCount($this->countRelations);
        }

        return $collection;
    }

    /**
     * @param $perPage
     * @param $pageName
     * @param $page
     * @return array
     */
    protected function prepareCurrentPage($perPage, $pageName, $page)
    {
        return [
            $page ?: Paginator::resolveCurrentPage($pageName),
            $perPage ?: $this->model->getPerPage()
        ];
    }

    /**
     * @param $method
     * @param $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, $this->interruptions)) {
            return $this->forwardCallTo($this->esBuilder, $method, $parameters);
        }
        $this->forwardCallTo($this->esBuilder, $method, $parameters);

        return $this;
    }

}
