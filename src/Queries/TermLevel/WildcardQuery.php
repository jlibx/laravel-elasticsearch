<?php


namespace Golly\Elastic\Queries\TermLevel;


use Golly\Elastic\Queries\Query;

/**
 * Class WildcardQuery
 * @package Golly\Elastic\Queries\TermLevel
 */
class WildcardQuery extends Query
{

    /**
     * WildcardQuery constructor.
     * @param string $field
     * @param string $value
     * @param array $params
     */
    public function __construct(string $field, string $value, array $params = [])
    {
        $this->field = $field;
        $this->value = $value;
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'wildcard';
    }

    /**
     * @return array
     */
    public function getOutput()
    {
        return [
            $this->field => $this->merge([
                'value' => $this->handleValue($this->value)
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
