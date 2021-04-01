<?php


namespace Golly\Elastic\Eloquent;

use Closure;
use Golly\Elastic\ElasticBuilder;
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
 * @mixin ElasticBuilder
 */
class Builder
{
    use ForwardsCalls;

    /**
     * @var ElasticBuilder
     */
    protected $query;

    /**
     * @var Model|HasElasticsearch
     */
    protected $model;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var array
     */
    protected $countRelations = [];

    /**
     * @var string[]
     */
    protected $interruptions = [
        'toSearchParams'
    ];

    /**
     * Builder constructor.
     * @param ElasticBuilder $query
     */
    public function __construct(ElasticBuilder $query)
    {
        $this->query = $query;
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @return $this
     * @throws ElasticException
     */
    public function must($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value);
    }

    /**
     * @param string $column
     * @param string $value
     * @return $this
     * @throws ElasticException
     */
    public function mustLike(string $column, string $value)
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
    public function where($column, $operator = null, $value = null, $type = 'must')
    {
        if ($column instanceof Closure && is_null($operator)) {
            $column($query = $this->model->newElasticQuery());

            $this->query->addBooleanWhereQuery($query->getQuery(), $type);
        } else {
            $this->query->where(...func_get_args());
        }

        return $this;
    }

    /**
     * @param string $column
     * @param string $value
     * @return $this
     * @throws ElasticException
     */
    public function whereLike(string $column, string $value)
    {
        return $this->where($column, 'like', $value);
    }

    /**
     * @param $column
     * @param string|null $operator
     * @param null $value
     * @return $this
     * @throws ElasticException
     */
    public function should($column, string $operator = null, $value = null)
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
     * @param $column
     * @param null $operator
     * @param null $value
     * @return $this
     * @throws ElasticException
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        [$value, $operator] = $this->query->prepareValueAndOperator(
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
        $builder = $this->model->newElasticQuery();
        $builder->setRelation($relation);
        $callback($builder);
        $tWheres = $builder->getQuery()->getBoolQueryWheres();
        foreach ($tWheres as $type => $wheres) {
            foreach ($wheres as $where) {
                $this->query->addToBoolQuery($where, $type);
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
     * @return Collection
     * @throws ElasticException
     */
    public function get(array $columns = [], array $options = [])
    {
        $entity = $this->getRaw($columns, $options);

        return $this->toCollection($entity);
    }


    /**
     * @param array $columns
     * @param array $options
     * @return ElasticEntity
     * @throws ElasticException
     */
    public function getRaw(array $columns = [], array $options = [])
    {
        if ($this->model->useSoftDelete()) {
            $this->query->where('__soft_deleted', 0);
        }
        return $this->query->addSelect($columns)->get($options);
    }

    /**
     * @param array $columns
     * @param array $options
     * @return Model|null
     * @throws ElasticException
     */
    public function first(array $columns = [], array $options = [])
    {
        $this->query->limit(1);

        return $this->get($columns, $options)->first();
    }

    /**
     * @param array $columns
     * @param array $options
     * @return ElasticEntity
     * @throws ElasticException
     */
    public function firstRaw(array $columns = [], array $options = [])
    {
        $this->query->limit(1);

        return $this->getRaw($columns, $options);
    }

    /**
     * @param null $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = [], $pageName = 'page', $page = null)
    {
        [$page, $prePage] = $this->prepareCurrentPage($perPage, $pageName, $page);
        $offset = ($page - 1) * $prePage;
        $entity = $this->query->addSelect($columns)->offset($offset)->limit($prePage)->get();
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
    public function paginateRaw($perPage = null, $columns = [], $pageName = 'page', $page = null)
    {
        [$page, $prePage] = $this->prepareCurrentPage($perPage, $pageName, $page);
        $offset = ($page - 1) * $prePage;
        $this->query->offset($offset)->limit($prePage);
        $entity = $this->getRaw($columns);

        return $entity->paginate($prePage, $page);
    }

    /**
     * @return void
     */
    public function dd()
    {
        $this->query->dd();
    }

    /**
     * @return ElasticBuilder
     */
    public function getQuery(): ElasticBuilder
    {
        return $this->query;
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
        $this->query->from($model->getSearchIndex());

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
            return $this->forwardCallTo($this->query, $method, $parameters);
        }
        $this->forwardCallTo($this->query, $method, $parameters);

        return $this;
    }

}
