<?php


namespace Golly\Elastic\Endpoints;


use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\Queries\Compound\BoolQuery;
use Golly\Elastic\Queries\FullText\MatchQuery;
use Golly\Elastic\Queries\TermLevel\ExistsQuery;
use Golly\Elastic\Queries\TermLevel\RangeQuery;
use Golly\Elastic\Queries\TermLevel\TermQuery;
use Golly\Elastic\Queries\TermLevel\TermsQuery;
use Golly\Elastic\Queries\TermLevel\WildcardQuery;

/**
 * Class QueryEndpoint
 * @package Golly\Elastic\Endpoints
 */
class QueryEndpoint extends Endpoint
{

    /**
     * @var string|null
     */
    protected $relation;

    /**
     * @var BoolQuery
     */
    protected $boolQuery;

    /**
     * QueryEndpoint constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->boolQuery = new BoolQuery($params);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'query';
    }

    /**
     * @return array|array[]|null
     */
    public function normalize()
    {
        if (!$this->boolQuery) {
            return null;
        }

        return $this->boolQuery->toArray();
    }

    /**
     * @param string|null $relation
     * @return void
     */
    public function setRelation(string $relation = null)
    {
        $this->relation = $relation;
    }

    /**
     * @return BoolQuery
     */
    public function getBoolQuery()
    {
        return $this->boolQuery;
    }


    /**
     * @param BoolQuery $query
     * @return void
     */
    public function setBoolQuery(BoolQuery $query)
    {
        $this->boolQuery = $query;
    }

    /**
     * @param QueryInterface $query
     * @param string $boolType
     * @return void
     */
    public function addToBoolQuery(QueryInterface $query, string $boolType = 'must')
    {
        $this->boolQuery->addQuery($query, $boolType);
    }

    /**
     * @param string $field
     * @param $boolType
     * @return void
     */
    public function addExistsToBoolQuery(string $field, $boolType)
    {
        $field = $this->prepareRelationField($field);
        $this->addToBoolQuery(new ExistsQuery($field), $boolType);
    }

    /**
     * @param string $field
     * @param array $values
     * @param $boolType
     */
    public function addTermsToBoolQuery(string $field, array $values, $boolType)
    {
        $field = $this->prepareRelationField($field);
        $this->addToBoolQuery(new TermsQuery($field, $values), $boolType);
    }

    /**
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @param string $boolType
     * @return void
     */
    public function addOpticalToBoolQuery(string $field, string $operator, $value, string $boolType)
    {
        $field = $this->prepareRelationField($field);
        if ($query = $this->toQuery($field, $operator, $value)) {
            $this->addToBoolQuery($query, $boolType);
        }
    }

    /**
     * @param string $field
     * @param array $value
     * @param $boolType
     * @return void
     */
    public function addBetweenToBoolQuery(string $field, array $value, $boolType)
    {
        sort($value);
        $field = $this->prepareRelationField($field);
        $this->addToBoolQuery(new RangeQuery($field, [
            RangeQuery::GTE => $value[0],
            RangeQuery::LTE => $value[1]
        ]), $boolType);
    }

    /**
     * @param string $field
     * @param string $operator
     * @param $value
     * @return QueryInterface|null
     */
    protected function toQuery(string $field, string $operator, $value)
    {
        $field = $this->prepareRelationField($field);
        switch ($operator) {
            case '=':
            case '!=':
            case '<>':
                return new TermQuery($field, $value);
            case '>':
                return new RangeQuery($field, [
                    RangeQuery::GT => $value
                ]);
            case '<':
                return new RangeQuery($field, [
                    RangeQuery::LT => $value
                ]);
            case '>=':
                return new RangeQuery($field, [
                    RangeQuery::GTE => $value
                ]);
            case '<=':
                return new RangeQuery($field, [
                    RangeQuery::LTE => $value
                ]);
            case 'match':
                return new MatchQuery($field, $value);
            case 'like':
            case 'wildcard':
                return new WildcardQuery($field, $value);
            default:
                return null;
        }
    }


    /**
     * @param string $field
     * @return string
     */
    protected function prepareRelationField(string $field)
    {
        if (!$this->relation || str_starts_with($field, $this->relation . '.')) {
            return $field;
        }

        return $this->relation . '.' . $field;
    }
}
