<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries\TermLevel;

use Golly\Elastic\Queries\Query;

/**
 * Class TermsQuery
 * @package Golly\Elastic\Queries\TermLevel
 */
class TermsQuery extends Query
{

    /**
     * TermsQuery constructor.
     * @param string $field
     * @param array $values
     * @param array $params
     */
    public function __construct(string $field, array $values, array $params = [])
    {
        $this->field = $field;
        $this->value = $values;
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'terms';
    }

    /**
     * @return array
     */
    public function getTypeValue(): array
    {
        return $this->merge([
            $this->field => $this->value,
        ]);
    }

}
