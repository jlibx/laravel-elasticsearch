<?php


namespace Golly\Elastic\DSL\Queries;


/**
 * Class TermQuery
 * @package Golly\Elastic\DSL\Queries
 */
class TermQuery extends AbstractQuery
{

    /**
     * @var string
     */
    protected $type = 'term';

    /**
     * @param string $field
     * @param string|int|float|bool $value
     * @param array $params
     */
    public function __construct(string $field, $value, array $params = [])
    {
        $this->field = $field;
        $this->value = $value;
        $this->setParams($params);
    }

    /**
     * @return array
     */
    public function output()
    {
        $query = $this->merge();
        if (empty($query)) {
            $query = $this->value;
        } else {
            $query['value'] = $this->value;
        }

        return [
            $this->field => $query,
        ];
    }
}
