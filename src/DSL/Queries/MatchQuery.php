<?php


namespace Golly\Elastic\DSL\Queries;

/**
 * Class MatchQuery
 * @package Golly\Elastic\DSL\Queries
 */
class MatchQuery extends AbstractQuery
{

    /**
     * @var string
     */
    protected $type = 'match';

    /**
     * MatchQuery constructor.
     * @param $field
     * @param $query
     * @param array $params
     */
    public function __construct($field, $query, array $params = [])
    {
        $this->field = $field;
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

        return [
            $this->field => $this->merge($query),
        ];
    }
}
