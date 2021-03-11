<?php


namespace Golly\Elastic\DSL\Queries;

/**
 * Class StringQuery
 * @package Golly\Elastic\DSL\Queries
 */
class StringQuery extends AbstractQuery
{

    /**
     * @var string
     */
    protected $type = 'query_string';

    /**
     * @param string $query
     * @param array $params
     */
    public function __construct(string $query, array $params = [])
    {
        $this->value = $query;
        $this->setParams($params);
    }

    /**
     * @return array
     */
    public function output()
    {
        $query = [
            'query' => $this->value,
        ];

        return $this->merge($query);
    }
}
