<?php


namespace Golly\Elastic\DSL\Queries;


use Closure;
use Golly\Elastic\Contracts\QueryInterface;
use stdClass;

/**
 * Class BoolQuery
 * @package Golly\Elastic\DSL\Queries
 */
class BoolQuery extends AbstractQuery
{

    const MUST = 'must';  // 与 AND 等价。
    const MUST_NOT = 'must_not'; // 与 NOT 等价
    const SHOULD = 'should'; // 与 OR 等价
    const FILTER = 'filter';

    /**
     * @var string
     */
    protected $type = 'bool';

    /**
     * @var array
     */
    protected $containers = [];

    /**
     * Constructor to prepare container.
     *
     * @param array $containers
     */
    public function __construct(array $containers = [])
    {
        foreach ($containers as $type => $queries) {
            $queries = is_array($queries) ? $queries : [$queries];

            array_walk($queries, function ($query) use ($type) {
                if (in_array($type, [self::MUST, self::MUST_NOT, self::SHOULD, self::FILTER])) {
                    $this->add($query, $type);
                }
            });
        }
    }

    /**
     * @return array|stdClass
     */
    public function output()
    {
        $output = [];
        foreach ($this->containers as $boolType => $builders) {
            /** @var QueryInterface $builder */
            foreach ($builders as $builder) {
                $output[$boolType][] = $builder->toArray();
            }
        }
        $output = $this->merge($output);

        if (empty($output)) {
            $output = new stdClass();
        }

        return $output;
    }

    /**
     * @param QueryInterface $query
     * @param string $type
     * @return void
     */
    public function add(QueryInterface $query, string $type)
    {
        $this->containers[$type][] = $query;
    }

    /**
     * @param QueryInterface $query
     * @return $this
     */
    public function must(QueryInterface $query)
    {
        $this->add($query, self::MUST);

        return $this;
    }

    /**
     * @param QueryInterface $query
     * @return $this
     */
    public function mustNot(QueryInterface $query)
    {
        $this->add($query, self::MUST_NOT);

        return $this;
    }

    /**
     * @param QueryInterface $query
     * @return $this
     */
    public function should(QueryInterface $query)
    {
        $this->add($query, self::SHOULD);

        return $this;
    }

    /**
     * @param QueryInterface $query
     * @return $this
     */
    public function filter(QueryInterface $query)
    {
        $this->add($query, self::FILTER);

        return $this;
    }

    /**
     * @param string|callable $field
     * @param null $operator
     * @param null $value
     * @return $this
     */
    public function where($field, $operator = null, $value = null)
    {
        if ($field instanceof Closure) {
            $boolQuery = new self();
            $field($boolQuery);
            $this->must($boolQuery);
        } else {
            [$value, $operator] = $this->prepareValueAndOperator(
                $value, $operator, func_num_args() === 2
            );
            switch ($operator) {
                case '=':
                    $this->must(
                        new TermQuery($field, $value)
                    );
                    break;
                case '>':
                    $this->must(
                        new RangeQuery($field, [
                            RangeQuery::GT => $value
                        ])
                    );
                    break;
                case '<':
                    $this->must(
                        new RangeQuery($field, [
                            RangeQuery::LT => $value
                        ])
                    );
                    break;
                case '>=':
                    $this->must(
                        new RangeQuery($field, [
                            RangeQuery::GTE => $value
                        ])
                    );
                    break;
                case '<=':
                    $this->must(
                        new RangeQuery($field, [
                            RangeQuery::LTE => $value
                        ])
                    );
                    break;
                case 'like':
                case 'match':
                    $this->must(new MatchQuery($field, $value));
                    break;
                case '!=':
                case '<>':
                    $this->mustNot(new TermQuery($field, $value));
                    break;
            }
        }

        return $this;
    }

    /**
     * @param string $filed
     * @param string $value
     * @return $this
     */
    public function whereLike(string $filed, string $value)
    {
        return $this->where($filed, 'like', $value);
    }

    /**
     * @param string $filed
     * @param string $value
     * @return $this
     */
    public function whereMatch(string $filed, string $value)
    {
        return $this->where($filed, 'match', $value);
    }

    /**
     * @param string|callable $field
     * @param null $operator
     * @param null $value
     * @return $this
     */
    public function orWhere($field, $operator = null, $value = null)
    {
        if ($field instanceof Closure) {
            $boolQuery = new self();
            $field($boolQuery);
            $this->should($boolQuery);
        } else {
            [$value, $operator] = $this->prepareValueAndOperator(
                $value, $operator, func_num_args() === 2
            );
            switch ($operator) {
                case '=':
                    $this->should(
                        new TermQuery($field, $value)
                    );
                    break;
                case '>':
                    $this->should(
                        new RangeQuery($field, [
                            RangeQuery::GT => $value
                        ])
                    );
                    break;
                case '<':
                    $this->should(
                        new RangeQuery($field, [
                            RangeQuery::LT => $value
                        ])
                    );
                    break;
                case '>=':
                    $this->should(
                        new RangeQuery($field, [
                            RangeQuery::GTE => $value
                        ])
                    );
                    break;
                case '<=':
                    $this->should(
                        new RangeQuery($field, [
                            RangeQuery::LTE => $value
                        ])
                    );
                    break;
                case 'like':
                case 'match':
                    $this->should(new MatchQuery($field, $value));
                    break;
            }
        }

        return $this;
    }

    /**
     * @param string $filed
     * @param string $value
     * @return $this
     */
    public function orWhereLike(string $filed, string $value)
    {
        return $this->orWhere($filed, 'like', $value);
    }

    /**
     * @param string $filed
     * @param string $value
     * @return $this
     */
    public function orWhereMatch(string $filed, string $value)
    {
        return $this->orWhere($filed, 'match', $value);
    }

    /**
     * @param $value
     * @param $operator
     * @param false $useDefault
     * @return array
     */
    protected function prepareValueAndOperator($value, $operator, $useDefault = false)
    {
        if ($useDefault) {
            return [$operator, '='];
        }

        return [$value, $operator];
    }

}
