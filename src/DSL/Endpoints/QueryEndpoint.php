<?php


namespace Golly\Elastic\DSL\Endpoints;


use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\DSL\Queries\BoolQuery;

/**
 * Class QueryEndpoint
 * @package Golly\Elastic\DSL\Endpoints
 */
class QueryEndpoint extends AbstractEndpoint
{
    /**
     * Endpoint name
     */
    const NAME = 'query';

    /**
     * @var BoolQuery
     */
    private $boolQuery;

    /**
     * @var bool
     */
    private $filterSet = false;

    /**
     * @var QueryInterface[]
     */
    protected $queries = [];

    /**
     * @return array
     */
    public function normalize()
    {
        if (!$this->filterSet && $this->getParam('filter_query')) {
            /** @var QueryInterface $filter */
            $filter = $this->getParam('filter_query');
            $this->addToBoolQuery($filter, BoolQuery::FILTER);
            $this->filterSet = true;
        }

        if (!$this->boolQuery) {
            return null;
        }

        return $this->boolQuery->toArray();
    }

    /**
     * @return QueryInterface[]
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @param QueryInterface $query
     * @return void
     */
    public function addQuery(QueryInterface $query)
    {
        $this->addToBoolQuery($query, BoolQuery::MUST);
    }

    /**
     * @param string $key
     * @return void
     */
    public function removeQuery(string $key)
    {
        if (isset($this->queries[$key])) {
            unset($this->queries[$key]);
        }
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
    public function addToBoolQuery(QueryInterface $query, string $boolType)
    {
        if (!$this->boolQuery) {
            $this->boolQuery = new BoolQuery();
        }

        $this->boolQuery->add($query, $boolType);
    }
}
