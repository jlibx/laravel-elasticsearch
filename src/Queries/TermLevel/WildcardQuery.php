<?php
declare(strict_types=1);

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
    public function getType(): string
    {
        return 'wildcard';
    }

    /**
     * @return array
     */
    public function getTypeValue(): array
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
        if ($this->hasMatched($value)) {
            return $value;
        }

        return '*' . $value . '*';
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function hasMatched(string $value)
    {
        return str_starts_with($value, '*') && str_ends_with($value, '*');
    }
}
