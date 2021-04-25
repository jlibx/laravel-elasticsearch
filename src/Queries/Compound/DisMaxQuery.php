<?php


namespace Golly\Elastic\Queries\Compound;


use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\Queries\Query;

/**
 * Class DisMaxQuery
 * @package Golly\Elastic\Queries\Compound
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-dis-max-query.html
 */
class DisMaxQuery extends Query
{

    /**
     * @var QueryInterface[]
     */
    protected $queries;

    /**
     * DisMaxQuery constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->setParams($params);
    }

    /**
     * @param QueryInterface $query
     * @return $this
     */
    public function addQuery(QueryInterface $query)
    {
        $this->queries[] = $query;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'dis_max';
    }

    /**
     * @return array
     */
    public function getTypeValue()
    {
        $queries = [];
        foreach ($this->queries as $type) {
            $queries[] = $type->toArray();
        }
        return $this->merge([
            'queries' => $queries
        ]);
    }
}
