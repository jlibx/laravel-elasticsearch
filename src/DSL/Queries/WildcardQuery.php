<?php


namespace Golly\Elastic\DSL\Queries;


/**
 * Class WildcardQuery
 * @package Golly\Elastic\DSL\Queries
 */
class WildcardQuery extends AbstractQuery
{

    /**
     * @var string
     */
    protected $type = 'wildcard';

    /**
     * WildcardQuery constructor.
     * @param string $field
     * @param string $value
     * @param array $params
     */
    public function __construct(string $field, string $value, array $params = [])
    {
        $this->field = $field;
        $this->value = $this->handleValue($value);
        $this->setParams($params);
    }

    /**
     * @return array
     */
    public function output()
    {
        return [
            $this->field => $this->merge([
                'value' => $this->value
            ]),
        ];
    }

    /**
     * @param string $value
     * @return string
     */
    protected function handleValue(string $value)
    {
        // 是否包含通配符
        if (str_contains($value, '?') || str_contains($value, '*')) {
            return $value;
        }

        return '*' . $value . '*';
    }
}
