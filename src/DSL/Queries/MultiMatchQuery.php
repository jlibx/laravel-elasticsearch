<?php


namespace Golly\Elastic\DSL\Queries;


/**
 * Class MultiMatchQuery
 * @package Golly\Elastic\DSL\Queries
 */
class MultiMatchQuery extends AbstractQuery
{

    /**
     * @var string
     */
    protected $type = 'multi_match';

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @param array $fields
     * @param string $query
     * @param array $params
     */
    public function __construct(array $fields, string $query, array $params = [])
    {
        $this->fields = $fields;
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
        if (count($this->fields)) {
            $query['fields'] = $this->fields;
        }

        return $this->merge($query);
    }
}
