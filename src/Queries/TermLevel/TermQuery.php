<?php


namespace Golly\Elastic\Queries\TermLevel;


use Golly\Elastic\Queries\Query;

/**
 * Class TermQuery
 * @package Golly\Elastic\Queries\TermLevel
 */
class TermQuery extends Query
{

    /**
     * TermQuery constructor.
     * @param string $field
     * @param mixed $value
     * @param array $params
     */
    public function __construct(string $field, mixed $value, array $params = [])
    {
        $this->field = $field;
        $this->value = $value;
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'term';
    }


    /**
     * @return array
     */
    public function getTypeValue(): array
    {
        if (empty($this->params)) {
            $params = $this->value;
        } else {
            $params = $this->merge([
                'value' => $this->value
            ]);
        }

        return [
            $this->field => $params,
        ];
    }
}
