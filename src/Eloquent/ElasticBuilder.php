<?php


namespace Golly\Elastic\Eloquent;


use Closure;
use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\DSL\Aggregations\Buckets\RangeBucket;
use Golly\Elastic\DSL\Aggregations\Buckets\TermsBucket;
use Golly\Elastic\DSL\Aggregations\Metrics\AvgMetric;
use Golly\Elastic\DSL\Aggregations\Metrics\CountMetric;
use Golly\Elastic\DSL\Aggregations\Metrics\MaxMetric;
use Golly\Elastic\DSL\Aggregations\Metrics\MinMetric;
use Golly\Elastic\DSL\Aggregations\Metrics\SumMetric;
use Golly\Elastic\DSL\Builder;
use Golly\Elastic\DSL\Sorts\FieldSort;
use Golly\Elastic\DSL\Queries\BoolQuery;
use Golly\Elastic\DSL\Queries\ExistsQuery;
use Golly\Elastic\DSL\Queries\RangeQuery;
use Golly\Elastic\DSL\Queries\TermsQuery;
use Golly\Elastic\Engines\ElasticEngine;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;

/**
 * Class ElasticBuilder
 * @package Golly\Elastic\Eloquent
 */
class ElasticBuilder
{
    use Macroable;

    /**
     * The model instance.
     *
     * @var Model|Searchable
     */
    public $model;

    /**
     * Optional callback before search execution.
     *
     * @var Closure|null
     */
    public $callback;

    /**
     * The custom index specified for the search.
     *
     * @var string
     */
    public $index;

    /**
     * The with array.
     *
     * @var array
     */
    public $relations = [];

    /**
     * @var ElasticEngine
     */
    protected $engine;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var BoolQuery
     */
    protected $boolQuery;


    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $perPage;

    /**
     * Create a new search builder instance.
     *
     * @param Model|Searchable $model
     * @param Closure|null $callback
     * @return void
     */
    public function __construct($model, $callback = null)
    {
        $this->model = $model;
        $this->callback = $callback;
        $this->index = $model->searchableIndex();
        $this->engine = new ElasticEngine();
        $this->builder = new Builder();
        $this->boolQuery = new BoolQuery();
    }

    /**
     * Specify a custom index to perform this search on.
     *
     * @param string $index
     * @return $this
     */
    public function setIndex(string $index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @param array $relations
     * @return $this
     */
    public function with(array $relations = [])
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function select($fields = [])
    {
        $this->builder->setSource(Arr::wrap($fields));

        return $this;
    }

    /**
     * Add a constraint to the search query.
     *
     * @param string|callable $field
     * @param string|null $operator
     * @param null $value
     * @return $this
     */
    public function where($field, $operator = null, $value = null)
    {
        $this->boolQuery->where(...func_get_args());

        return $this;
    }

    /**
     * @param string $relation
     * @param Closure $callback
     * @return $this
     */
    public function whereHas(string $relation, Closure $callback)
    {
        $this->boolQuery->setRelation($relation);
        $callback($this->boolQuery);
        $this->boolQuery->setRelation();

        return $this;
    }

    /**
     * @param string|callable $field
     * @param string|null $operator
     * @param string|null $value
     * @return $this
     */
    public function orWhere($field, $operator = null, $value = null)
    {
        $this->boolQuery->orWhere(...func_get_args());

        return $this;
    }

    /**
     * @param string $field
     * @param array $value
     * @return $this
     */
    public function whereIn(string $field, array $value)
    {
        $this->boolQuery->must(
            new TermsQuery($field, $value)
        );

        return $this;
    }

    /**
     * @param string $field
     * @param array $value
     * @return $this
     */
    public function whereNotIn(string $field, array $value)
    {
        $this->boolQuery->mustNot(new TermsQuery($field, $value));

        return $this;
    }

    /**
     * @param string $field
     * @param array $value
     * @return $this
     */
    public function whereBetween(string $field, array $value)
    {
        if (count($value) == 2) {
            $this->boolQuery->must(
                new RangeQuery($field, [
                    RangeQuery::GTE => $value[0],
                    RangeQuery::LTE => $value[1],
                ])
            );
        }

        return $this;
    }

    /**
     * @param string $field
     * @param array $value
     * @return $this
     */
    public function whereNotBetween(string $field, array $value)
    {
        if (count($value) == 2) {
            $this->boolQuery->mustNot(
                new RangeQuery($field, [
                    RangeQuery::GTE => $value[0],
                    RangeQuery::LTE => $value[1],
                ])
            );
        }

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function whereExists(string $field)
    {
        $this->boolQuery->must(new ExistsQuery($field));

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function whereNotExists(string $field)
    {
        $this->boolQuery->mustNot(new ExistsQuery($field));

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @return $this
     */
    public function whereLike(string $field, string $value)
    {
        $this->boolQuery->where($field, 'like', $value);

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @return $this
     */
    public function whereMatch(string $field, string $value)
    {
        $this->boolQuery->where($field, 'match', $value);

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @return $this
     */
    public function orWhereLike(string $field, string $value)
    {
        $this->boolQuery->orWhere($field, 'like', $value);

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @return $this
     */
    public function orWhereMatch(string $field, string $value)
    {
        $this->boolQuery->orWhere($field, 'match', $value);

        return $this;
    }

    /**
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $field, $direction = 'asc')
    {
        $this->builder->addSort(
            new FieldSort($field, $direction)
        );

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function count(string $field)
    {
        $this->builder->addAggregation(
            new CountMetric($field)
        );

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function sum(string $field)
    {
        $this->builder->addAggregation(
            new SumMetric($field)
        );

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function avg(string $field)
    {
        $this->builder->addAggregation(
            new AvgMetric($field)
        );

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function mix(string $field)
    {
        $this->builder->addAggregation(
            new MinMetric($field)
        );

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function max(string $field)
    {
        $this->builder->addAggregation(
            new MaxMetric($field)
        );

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function groupByTerms(string $field)
    {
        $this->builder->addAggregation(
            new TermsBucket($field)
        );

        return $this;
    }

    /**
     * @param string $field
     * @param array $ranges
     * @return $this
     */
    public function groupByRanges(string $field, array $ranges = [])
    {
        $this->builder->addAggregation(
            new RangeBucket($field, $ranges)
        );

        return $this;
    }

    /**
     * Set the "limit" for the search query.
     *
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): ElasticBuilder
    {
        $this->builder->setSize($limit);

        return $this;
    }

    /**
     * Explain the request.
     *
     * @return array
     */
    public function explain()
    {
        $this->builder->setExplain(true);

        return $this->engine->search($this);
    }

    /**
     * Set the min_score on the filter.
     *
     * @param int $score
     * @return $this
     */
    public function minScore(int $score)
    {
        $this->builder->setMinScore($score);

        return $this;
    }

    /**
     * @return array
     */
    public function getBuilderQuery()
    {
        return $this->builder->setBoolQuery(
            $this->boolQuery
        )->toArray();
    }

    /**
     * @return ElasticEngine
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Get the raw results of the search.
     *
     * @return array
     */
    public function raw()
    {
        return $this->engine->search($this);
    }

    /**
     * Get the keys of search results.
     *
     * @return array
     */
    public function keys()
    {
        return $this->engine->keys($this);
    }

    /**
     * @return array
     */
    public function firstRaw()
    {
        return $this->limit(1)->raw();
    }

    /**
     * Get the first result from the search.
     *
     * @return Model
     */
    public function first()
    {
        return $this->limit(1)->get()->first();
    }

    /**
     * Get the results of the search.
     *
     * @return Collection
     */
    public function get()
    {
        $collection = $this->engine->get($this);
        if ($this->relations) {
            $collection->loadMissing($this->relations);
        }

        return $collection;
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param int $perPage
     * @param string $pageName
     * @param int|null $page
     * @return Paginator
     */
    public function simplePaginate($perPage = null, $pageName = 'page', $page = null)
    {
        $this->preparePageParams($perPage, $pageName, $page);
        $raws = $this->engine->search($this);
        $collection = $this->engine->map($this, $raws, $this->model);
        $total = $this->engine->getTotalCount($raws);
        if ($this->relations && $total > 0) {
            $collection->load($this->relations);
        }
        $hasMorePages = ($this->perPage * $this->page) < $total;

        return (new Paginator($collection, $this->perPage, $this->page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]))->hasMorePagesWhen($hasMorePages);
    }

    /**
     * Paginate the given query into a paginator.
     *
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = null, $pageName = 'page', $page = null)
    {
        $this->preparePageParams($perPage, $pageName, $page);
        $raws = $this->engine->search($this);
        $collection = $this->engine->map($this, $raws, $this->model);
        $total = $this->engine->getTotalCount($raws);
        if ($this->relations && $total > 0) {
            $collection->loadMissing($this->relations);
        }

        return new LengthAwarePaginator(
            $collection,
            $total,
            $this->perPage,
            $this->page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }

    /**
     * Paginate the given query into a simple paginator with raw data.
     *
     * @param int $perPage
     * @param string $pageName
     * @param int|null $page
     * @return LengthAwarePaginator
     */
    public function paginateRaw($perPage = null, $pageName = 'page', $page = null)
    {
        $this->preparePageParams($perPage, $pageName, $page);
        $raws = $this->engine->search($this);
        $total = $this->engine->getTotalCount($raws);

        return new LengthAwarePaginator($raws, $total, $this->perPage, $this->page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * @return void
     */
    public function dd()
    {
        dd($this->getBuilderQuery());
    }

    /**
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return void
     */
    protected function preparePageParams($perPage = null, $pageName = 'page', $page = null)
    {
        $this->page = $page ?: Paginator::resolveCurrentPage($pageName);
        $this->perPage = $perPage ?: $this->model->getPerPage();

        $this->builder->setFrom(($this->page - 1) * $perPage);
        $this->builder->setSize($this->perPage);
    }
}
