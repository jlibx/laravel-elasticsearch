<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries\TermLevel;

use Golly\Elastic\Queries\Query;

/**
 * Class PrefixQuery
 * @package Golly\Elastic\Queries\TermLevel
 */
class PrefixQuery extends Query
{

    /**
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
        return 'prefix';
    }

    /**
     * @return array
     */
    public function getTypeValue(): array
    {
        return [
            $this->field => $this->merge([
                'value' => $this->value,
            ]),
        ];
    }
}
