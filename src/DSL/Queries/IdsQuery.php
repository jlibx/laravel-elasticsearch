<?php


namespace Golly\Elastic\DSL\Queries;

/**
 * Class IdsQuery
 * @package Golly\Elastic\DSL\Queries
 */
class IdsQuery extends AbstractQuery
{

    /**
     * @var string
     */
    protected $type = 'ids';

    /**
     * @param array $values
     * @param array $params
     */
    public function __construct(array $values, array $params = [])
    {
        $this->value = $values;

        $this->setParams($params);
    }

    /**
     * @return array
     */
    public function output()
    {
        return $this->merge([
            'values' => $this->value,
        ]);
    }


}
