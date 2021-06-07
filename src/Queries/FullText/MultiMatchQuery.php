<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries\FullText;

use Golly\Elastic\Queries\Query;

/**
 * Class MultiMatchQuery
 * @package Golly\Elastic\Queries\FullText
 */
class MultiMatchQuery extends Query
{

    /**
     * @var array
     */
    protected array $fields = [];

    /**
     * MultiMatchQuery constructor.
     * @param array $fields
     * @param string $value
     * @param array $params
     */
    public function __construct(array $fields, string $value, array $params = [])
    {
        $this->fields = $fields;
        $this->value = $value;
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'multi_match';
    }

    /**
     * @return array
     */
    public function getTypeValue(): array
    {
        $query = [
            'query' => $this->value,
        ];
        if (count($this->fields)) {
            $query['fields'] = $this->fields;
        }

        return $this->merge($query);
    }
}
