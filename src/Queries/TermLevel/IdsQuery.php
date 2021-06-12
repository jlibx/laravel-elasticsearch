<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries\TermLevel;

use Golly\Elastic\Queries\Query;

/**
 * Class IdsQuery
 * @package Golly\Elastic\Queries\TermLevel
 */
class IdsQuery extends Query
{

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
     * @return string
     */
    public function getType(): string
    {
        return 'ids';
    }

    /**
     * @return array
     */
    public function getTypeValue(): array
    {
        return $this->merge([
            'values' => $this->value,
        ]);
    }
}
