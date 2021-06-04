<?php


namespace Golly\Elastic\Queries\TermLevel;


use Golly\Elastic\Queries\Query;

/**
 * Class RangeQuery
 * @package Golly\Elastic\Queries\TermLevel
 */
class RangeQuery extends Query
{

    public const LT = 'lt';
    public const GT = 'gt';
    public const LTE = 'lte';
    public const GTE = 'gte';

    /**
     * RangeQuery constructor.
     * @param $field
     * @param array $params
     */
    public function __construct($field, array $params = [])
    {
        $this->field = $field;
        $this->setParams($params);
    }

    /**
     * @param array ranges
     * @return $this
     */
    public function setRanges(array $ranges): self
    {
        foreach ($ranges as $key => $value) {
            $this->addParam($key, $value);
        }

        return $this;
    }

    /**
     * @param int|float $value
     * @return $this
     */
    public function setLtValue(int|float $value): self
    {
        $this->setParams([static::LT => $value]);

        return $this;
    }

    /**
     * @param int|float $value
     * @return $this
     */
    public function setLteValue(int|float $value): self
    {
        $this->setParams([static::LTE => $value]);

        return $this;
    }

    /**
     * @param int|float $value
     * @return $this
     */
    public function setGtValue(int|float $value): self
    {
        $this->setParams([static::GT => $value]);

        return $this;
    }

    /**
     * @param int|float $value
     * @return $this
     */
    public function setGteValue(int|float $value): self
    {
        $this->setParams([static::GTE => $value]);

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'range';
    }

    /**
     * @return array
     */
    public function getTypeValue(): array
    {
        return [
            $this->field => $this->params,
        ];
    }


}
