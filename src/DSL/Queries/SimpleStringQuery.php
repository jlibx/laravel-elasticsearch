<?php


namespace Golly\Elastic\DSL\Queries;


/**
 * Class SimpleStringQuery
 * @package Golly\Elastic\DSL\Queries
 */
class SimpleStringQuery extends AbstractQuery
{

    /**
     * @var string
     */
    protected $type = 'simple_query_string';

    /**
     * SimpleStringQuery constructor.
     * @param $query
     * @param array $params
     */
    public function __construct($query, array $params = [])
    {
        $this->value = $query;
        $this->setParams($params);
    }

    /**
     * @return array
     */
    public function output()
    {
        return $this->merge([
            'query' => $this->value
        ]);
    }
}
