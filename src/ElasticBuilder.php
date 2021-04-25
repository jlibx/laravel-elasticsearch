<?php


namespace Golly\Elastic;

use Closure;
use Golly\Elastic\Contracts\AggregationInterface;
use Golly\Elastic\Contracts\EndpointInterface;
use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\Contracts\SortInterface;
use Golly\Elastic\Endpoints\AggregationEndpoint;
use Golly\Elastic\Endpoints\HighlightEndpoint;
use Golly\Elastic\Endpoints\QueryEndpoint;
use Golly\Elastic\Endpoints\SortEndpoint;
use Golly\Elastic\Exceptions\ElasticException;
use Golly\Elastic\Queries\Compound\BoolQuery;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

/**
 * Class ElasticBuilder
 * @package Golly\Elastic
 */
class ElasticBuilder
{

    /**
     * The index that should be returned.
     *
     * @var string
     */
    public $index;

    /**
     * The columns that should be returned.
     *
     * @var array
     */
    public $columns = [];

    /**
     * The number of records to skip.
     *
     * @var int
     */
    public $offset;

    /**
     * The maximum number of records to return.
     *
     * @var int
     */
    public $limit;

    /**
     * @var bool
     */
    public $explain = false;

    /**
     * @var
     */
    public $version;

    /**
     * @var QueryEndpoint
     */
    public $queryEndpoint;

    /**
     * @var SortEndpoint
     */
    public $sortEndpoint;

    /**
     * @var AggregationEndpoint
     */
    public $aggregationEndpoint;

    /**
     * @var HighlightEndpoint
     */
    public $highlightEndpoint;

    /**
     * @var ElasticEngine
     */
    protected $elasticEngine;

    /**
     * @var string[]
     */
    protected $operators = [
        '=', '>', '>=', '<', '<=', '!=', '<>',
        'term', 'match', 'range',
        'wildcard', 'like'
    ];

    /**
     * @var string[]
     */
    protected $params = [
        'index' => 'index',
        'columns' => '_source',
        'offset' => 'from',
        'limit' => 'size',
        'storedFields' => 'stored_fields',
        'scriptFields' => 'script_fields',
        'explain' => 'explain',
        'version' => 'version',
        'indicesBoost' => 'indices_boost',
        'minScore' => 'min_score',
        'searchAfter' => 'search_after',
        'trackTotalHits' => 'track_total_hits',
    ];

    /**
     * ElasticBuilder constructor.
     */
    public function __construct()
    {
        $this->queryEndpoint = new QueryEndpoint();
        $this->sortEndpoint = new SortEndpoint();
        $this->aggregationEndpoint = new AggregationEndpoint();
        $this->highlightEndpoint = new HighlightEndpoint();
    }

    /**
     * @param string $index
     * @return $this
     */
    public function from(string $index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function select(array $columns = [])
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function addSelect(array $columns = [])
    {
        $this->columns = array_merge($this->columns, $columns);

        return $this;
    }

    /**
     * @param mixed $column
     * @param string|mixed $operator
     * @param mixed $value
     * @param string $type
     * @return $this
     * @throws ElasticException
     */
    public function where($column, string $operator = null, $value = null, string $type = 'must')
    {
        if (is_array($column)) {
            return $this->addArrayOfWheres($column, $type);
        }
        // 预处理操作符和查询值
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );
        if ($column instanceof Closure && is_null($operator)) {
            return $this->whereBoolean($column, $type);
        }
        if ($column instanceof QueryInterface) {
            $this->queryEndpoint->addToBoolQuery($column, $type);
        }
        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        if (is_null($value)) {
            return $this->whereNull($column, $operator !== '=');
        }
        if (in_array($operator, ['!=', '<>'])) {
            $type = 'must_not';
        }
        $this->queryEndpoint->addOpticalToBoolQuery((string)$column, $operator, $value, $type);

        return $this;
    }

    /**
     * @param string $column
     * @param mixed $operator
     * @param null $value
     * @return $this
     * @throws ElasticException
     */
    public function must(string $column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value);
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
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->where($column, $operator, $value, BoolQuery::SHOULD);
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
     * @param Arrayable|array $values
     * @return $this
     */
    public function whereIn(string $column, $values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }
        $this->queryEndpoint->addTermsToBoolQuery($column, $values, BoolQuery::MUST);

        return $this;
    }

    /**
     * @param string $column
     * @param Arrayable|array $values
     * @return $this
     */
    public function whereNotIn(string $column, $values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }
        $this->queryEndpoint->addTermsToBoolQuery($column, $values, BoolQuery::MUST_NOT);

        return $this;
    }

    /**
     * @param $columns
     * @param false $not
     * @return $this
     */
    public function whereNull($columns, $not = false)
    {
        $type = $not ? 'must_not' : 'must';
        foreach (Arr::wrap($columns) as $column) {
            $this->queryEndpoint->addExistsToBoolQuery($column, $type);
        }

        return $this;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function whereNotNull($columns)
    {
        return $this->whereNull($columns, true);
    }

    /**
     * @param string $column
     * @param array $values
     * @param string $type
     * @return $this
     */
    public function whereBetween(string $column, array $values, $type = 'must')
    {
        $values = array_slice($values, 0, 2);
        if (count($values) == 2) {
            $this->queryEndpoint->addBetweenToBoolQuery($column, $values, $type);
        }

        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return $this
     */
    public function orWhereBetween($column, array $values)
    {
        return $this->whereBetween($column, $values, BoolQuery::SHOULD);
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function whereNotBetween(string $column, array $values)
    {
        return $this->whereBetween($column, $values, BoolQuery::MUST_NOT);
    }

    /**
     * TODO 优化逻辑，有点绕
     *
     * @param string $relation
     * @param Closure $callback
     * @return $this
     */
    public function whereHas(string $relation, Closure $callback)
    {
        $query = $this->newQuery();
        $query->setRelation($relation);
        $callback($query);
        $tWheres = $query->getBoolQueryWheres();
        foreach ($tWheres as $type => $wheres) {
            foreach ($wheres as $where) {
                $this->addToBoolQuery($where, $type);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function newQuery()
    {
        return new static();
    }

    /**
     * Add a nested where statement to the query.
     *
     * @param Closure $callback
     * @param string $type
     * @return $this
     */
    public function whereBoolean(Closure $callback, $type = 'must')
    {
        call_user_func($callback, $query = $this->newQuery());

        return $this->addBooleanWhereQuery($query, $type);
    }

    /**
     * @param ElasticBuilder $query
     * @param string $type
     * @return $this
     */
    public function addBooleanWhereQuery(ElasticBuilder $query, $type = 'must')
    {
        if ($bQuery = $query->getBoolQuery()) {
            $this->queryEndpoint->addToBoolQuery($bQuery, $type);
        }

        return $this;
    }

    /**
     * Return the search definition using the Query DSL
     *
     * @return BoolQuery
     */
    public function getBoolQuery()
    {
        return $this->queryEndpoint->getBoolQuery();
    }

    /**
     * @return array
     */
    public function getBoolQueryWheres()
    {
        return $this->queryEndpoint->getBoolQuery()->wheres;
    }

    /**
     * @param QueryInterface $query
     * @param string $type
     * @return $this
     */
    public function addToBoolQuery(QueryInterface $query, $type = 'must')
    {
        $this->queryEndpoint->addToBoolQuery($query, $type);

        return $this;
    }

    /**
     * @param SortInterface|string $column
     * @param string $direction
     * @return $this
     * @throws ElasticException
     */
    public function orderBy($column, $direction = 'asc')
    {
        if ($column instanceof SortInterface) {
            $this->sortEndpoint->addContainer($column);
        } else {
            $direction = strtolower($direction);
            if (!in_array($direction, ['asc', 'desc'], true)) {
                throw new ElasticException('Order direction must be "asc" or "desc".');
            }
            $this->sortEndpoint->addFieldSort($column, $direction);
        }

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     * @throws ElasticException
     */
    public function orderByDesc(string $column)
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     */
    public function highlight(string $column, array $params = [])
    {
        $this->highlightEndpoint->addField($column, $params);

        return $this;
    }

    /**
     * @param AggregationInterface|string $column
     * @param string $type
     * @return $this
     */
    public function aggregation($column, string $type)
    {
        if ($column instanceof AggregationInterface) {
            $this->aggregationEndpoint->addContainer($column);
        } else {
            $this->aggregationEndpoint->addAggregation($column, $type);
        }

        return $this;
    }

    /**
     * @param string $column
     * @param array $ranges
     * @return $this
     */
    public function range(string $column, array $ranges)
    {
        $this->aggregationEndpoint->addRangeBucket($column, $ranges);

        return $this;
    }


    /**
     * @param string $column
     * @return $this
     */
    public function stats(string $column)
    {
        return $this->aggregation($column, 'stats');
    }

    /**
     * @param string $column
     * @return $this
     */
    public function sum(string $column)
    {
        return $this->aggregation($column, 'sum');
    }

    /**
     * @param string $column
     * @return $this
     */
    public function min(string $column)
    {
        return $this->aggregation($column, 'min');
    }

    /**
     * @param string $column
     * @return $this
     */
    public function max(string $column)
    {
        return $this->aggregation($column, 'max');
    }

    /**
     * @param string $column
     * @return $this
     */
    public function avg(string $column)
    {
        return $this->aggregation($column, 'avg');
    }

    /**
     * @param array $options
     * @return Hydrate\ElasticEntity
     */
    public function get(array $options = [])
    {
        return $this->newElasticEngine()->search($options);
    }

    /**
     * @param array $options
     * @return Hydrate\ElasticEntity
     */
    public function first(array $options = [])
    {
        return $this->limit(1)->get($options);
    }

    /**
     * @param Collection $models
     * @return void
     */
    public function update(Collection $models)
    {
        $this->newElasticEngine()->update($models);
    }

    /**
     * @param Collection $models
     * @return void
     */
    public function delete(Collection $models)
    {
        $this->newElasticEngine()->delete($models);
    }

    /**
     * @return ElasticEngine
     */
    public function newElasticEngine(): ElasticEngine
    {
        if (!$this->elasticEngine) {
            $this->elasticEngine = new ElasticEngine();
            $this->elasticEngine->setBuilder($this);
        }

        return $this->elasticEngine;
    }

    /**
     * @return array
     */
    public function toSearchParams()
    {
        $result = [];
        foreach ($this->params as $field => $param) {
            $result[$param] = $this->{$field} ?? null;
        }
        /** @var EndpointInterface[] $endpoints */
        $endpoints = [
            $this->queryEndpoint,
            $this->sortEndpoint,
            $this->highlightEndpoint,
            $this->aggregationEndpoint
        ];
        foreach ($endpoints as $endpoint) {
            if ($output = $endpoint->normalize()) {
                $result['body'][$endpoint->getName()] = $output;
            }
        }

        return array_filter($result);
    }

    /**
     * @return void
     */
    public function dd()
    {
        dd($this->toSearchParams());
    }

    /**
     * Prepare the value and operator for a where clause.
     *
     * @param mixed $value
     * @param mixed $operator
     * @param bool $useDefault
     * @return array
     * @throws ElasticException
     */
    public function prepareValueAndOperator($value = null, $operator = null, $useDefault = false)
    {
        if ($useDefault) {
            return [$operator, '='];
        } elseif ($this->invalidOperatorAndValue($operator, $value)) {
            throw new ElasticException('Illegal operator and value combination.');
        }

        return [$value, $operator];
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param string|null $relation
     * @return void
     */
    public function setRelation(string $relation = null)
    {
        $this->queryEndpoint->setRelation($relation);
    }

    /**
     * Add an array of where clauses to the query.
     *
     * @param array $column
     * @param string $occur
     * @return $this
     */
    protected function addArrayOfWheres(array $column, string $occur)
    {
        $this->whereBoolean(function (ElasticBuilder $query) use ($column, $occur) {
            foreach ($column as $key => $value) {
                if (is_numeric($key) && is_array($value)) {
                    $query->where(...array_values($value));
                } else {
                    $query->where($key, '=', $value, $occur);
                }
            }
        });

        return $this;
    }

    /**
     * @param $operator
     * @param $value
     * @return bool
     */
    protected function invalidOperatorAndValue($operator, $value)
    {
        return is_null($value) && in_array($operator, $this->operators);
    }

    /**
     * Determine if the given operator is supported.
     *
     * @param string $operator
     * @return bool
     */
    protected function invalidOperator(string $operator)
    {
        return !in_array(strtolower($operator), $this->operators, true);
    }
}
