<?php
declare(strict_types=1);

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
use Golly\Elastic\Hydrate\ElasticEntity;
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
    public string $index;

    /**
     * The columns that should be returned.
     *
     * @var array
     */
    public array $columns = [];

    /**
     * The number of records to skip.
     *
     * @var int
     */
    public int $offset;

    /**
     * The maximum number of records to return.
     *
     * @var int
     */
    public int $limit;

    /**
     * @var bool
     */
    public bool $explain = false;

    /**
     * @var float
     */
    public float $version;

    /**
     * @var QueryEndpoint
     */
    public QueryEndpoint $queryEndpoint;

    /**
     * @var SortEndpoint
     */
    public SortEndpoint $sortEndpoint;

    /**
     * @var AggregationEndpoint
     */
    public AggregationEndpoint $aggregationEndpoint;

    /**
     * @var HighlightEndpoint
     */
    public HighlightEndpoint $highlightEndpoint;

    /**
     * @var ElasticEngine
     */
    protected ElasticEngine $elasticEngine;

    /**
     * @var string[]
     */
    protected array $operators = [
        '=', '>', '>=', '<', '<=', '!=', '<>',
        'term', 'match', 'range',
        'wildcard', 'like'
    ];

    /**
     * @var string[]
     */
    protected array $params = [
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
    public function from(string $index): static
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function select(array $columns = []): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function addSelect(array $columns = []): static
    {
        $this->columns = array_merge($this->columns, $columns);

        return $this;
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @param string $type
     * @return $this
     * @throws ElasticException
     */
    public function where($column, $operator = null, $value = null, string $type = 'must'): static
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
        if ($this->isInvalidOperator($operator)) {
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
    public function must(string $column, $operator = null, $value = null): static
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
    public function orWhere($column, $operator = null, $value = null): static
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
    public function should($column, $operator = null, $value = null): static
    {
        return $this->orWhere($column, $operator, $value);
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function whereIn(string $column, array $values): static
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }
        $this->queryEndpoint->addTermsToBoolQuery($column, $values, BoolQuery::MUST);

        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function whereNotIn(string $column, array $values): self
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }
        $this->queryEndpoint->addTermsToBoolQuery($column, $values, BoolQuery::MUST_NOT);

        return $this;
    }

    /**
     * @param array $columns
     * @param false $not
     * @return $this
     */
    public function whereNull(array $columns, bool $not = false): static
    {
        $type = $not ? BoolQuery::MUST_NOT : BoolQuery::MUST;
        foreach (Arr::wrap($columns) as $column) {
            $this->queryEndpoint->addExistsToBoolQuery($column, $type);
        }

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function whereNotNull(array $columns): static
    {
        return $this->whereNull($columns, true);
    }

    /**
     * @param string $column
     * @param int|float $min
     * @param int|float $max
     * @param string $type
     * @return $this
     */
    public function whereBetween(string $column, int|float $min, int|float $max, string $type = 'must'): static
    {
        $this->queryEndpoint->addBetweenToBoolQuery($column, $min, $max, $type);

        return $this;
    }

    /**
     * @param string $column
     * @param int|float $min
     * @param int|float $max
     * @return $this
     */
    public function shouldBetween(string $column, int|float $min, int|float $max): static
    {
        return $this->whereBetween($column, $min, $max, BoolQuery::SHOULD);
    }

    /**
     * @param string $column
     * @param int|float $min
     * @param int|float $max
     * @return $this
     */
    public function whereNotBetween(string $column, int|float $min, int|float $max): static
    {
        return $this->whereBetween($column, $min, $max, BoolQuery::MUST_NOT);
    }

    /**
     * TODO 优化逻辑，有点绕
     *
     * @param string $relation
     * @param callable $callback
     * @return $this
     */
    public function whereHas(string $relation, callable $callback): self
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
    public function newQuery(): static
    {
        return new static();
    }

    /**
     * Add a nested where statement to the query.
     *
     * @param callable $callback
     * @param string $type
     * @return $this
     */
    public function whereBoolean(callable $callback, string $type = 'must'): static
    {
        call_user_func($callback, $query = $this->newQuery());

        return $this->addBooleanWhereQuery($query, $type);
    }

    /**
     * @param ElasticBuilder $query
     * @param string $type
     * @return $this
     */
    public function addBooleanWhereQuery(ElasticBuilder $query, string $type = 'must'): static
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
    public function getBoolQuery(): BoolQuery
    {
        return $this->queryEndpoint->getBoolQuery();
    }

    /**
     * @return array
     */
    public function getBoolQueryWheres(): array
    {
        return $this->queryEndpoint->getBoolQuery()->wheres;
    }

    /**
     * @param QueryInterface $query
     * @param string $type
     * @return $this
     */
    public function addToBoolQuery(QueryInterface $query, string $type = 'must'): static
    {
        $this->queryEndpoint->addToBoolQuery($query, $type);

        return $this;
    }

    /**
     * @param string|SortInterface $column
     * @param string $direction
     * @return $this
     * @throws ElasticException
     */
    public function orderBy(SortInterface|string $column, string $direction = 'asc'): static
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
    public function orderByDesc(string $column): static
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     */
    public function highlight(string $column, array $params = []): static
    {
        $this->highlightEndpoint->addField($column, $params);

        return $this;
    }

    /**
     * @param string|AggregationInterface $column
     * @param string $type
     * @return $this
     */
    public function aggregation(AggregationInterface|string $column, string $type): static
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
    public function range(string $column, array $ranges): static
    {
        $this->aggregationEndpoint->addRangeBucket($column, $ranges);

        return $this;
    }


    /**
     * @param string $column
     * @return $this
     */
    public function stats(string $column): static
    {
        return $this->aggregation($column, 'stats');
    }

    /**
     * @param string $column
     * @return $this
     */
    public function sum(string $column): static
    {
        return $this->aggregation($column, 'sum');
    }

    /**
     * @param string $column
     * @return $this
     */
    public function min(string $column): static
    {
        return $this->aggregation($column, 'min');
    }

    /**
     * @param string $column
     * @return $this
     */
    public function max(string $column): static
    {
        return $this->aggregation($column, 'max');
    }

    /**
     * @param string $column
     * @return $this
     */
    public function avg(string $column): static
    {
        return $this->aggregation($column, 'avg');
    }

    /**
     * @param array $options
     * @return Hydrate\ElasticEntity
     */
    public function get(array $options = []): ElasticEntity
    {
        return $this->newElasticEngine()->search($options);
    }

    /**
     * @param array $options
     * @return Hydrate\ElasticEntity
     */
    public function first(array $options = []): ElasticEntity
    {
        return $this->limit(1)->get($options);
    }

    /**
     * @param Collection $models
     * @return bool
     */
    public function update(Collection $models): bool
    {
        return $this->newElasticEngine()->update($models);
    }

    /**
     * @param Collection $models
     * @return bool
     */
    public function delete(Collection $models): bool
    {
        return $this->newElasticEngine()->delete($models);
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
    public function toSearchParams(): array
    {
        $result = [];
        foreach ($this->params as $field => $param) {
            if ($value = $this->{$field}) {
                $result[$param] = $value;
            }
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

        return $result;
    }

    /**
     * Prepare the value and operator for a where clause.
     *
     * @param mixed|null $value
     * @param mixed|null $operator
     * @param bool $useDefault
     * @return array
     * @throws ElasticException
     */
    public function prepareValueAndOperator(mixed $value = null, mixed $operator = null, bool $useDefault = false): array
    {
        if ($useDefault) {
            return [$operator, '='];
        } elseif ($this->isInvalidOperatorAndValue($operator, $value)) {
            throw new ElasticException('Illegal operator and value combination.');
        }

        return [$value, $operator];
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param string $relation
     * @return $this
     */
    public function setRelation(string $relation): static
    {
        $this->queryEndpoint->setRelation($relation);

        return $this;
    }

    /**
     * Add an array of where clauses to the query.
     *
     * @param array $column
     * @param string $occur
     * @return $this
     */
    protected function addArrayOfWheres(array $column, string $occur): static
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
     * @param string $operator
     * @param mixed $value
     * @return bool
     */
    protected function isInvalidOperatorAndValue(string $operator, mixed $value): bool
    {
        return is_null($value) && in_array($operator, $this->operators);
    }

    /**
     * Determine if the given operator is supported.
     *
     * @param string $operator
     * @return bool
     */
    protected function isInvalidOperator(string $operator): bool
    {
        return !in_array(strtolower($operator), $this->operators, true);
    }
}
