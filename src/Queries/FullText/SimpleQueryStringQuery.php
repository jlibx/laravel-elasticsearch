<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries\FullText;

use Golly\Elastic\Queries\Query;

/**
 * Class SimpleQueryStringQuery
 * @package Golly\Elastic\Queries\FullText
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-simple-query-string-query.html
 */
class SimpleQueryStringQuery extends Query
{

    /**
     * SimpleQueryStringQuery constructor.
     * @param string $query
     * @param array $params
     */
    public function __construct(string $query, array $params = [])
    {
        $this->value = $query;
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'simple_query_string';
    }

    /**
     * @return array
     */
    public function getTypeValue(): array
    {
        return $this->merge([
            'query' => $this->value
        ]);
    }

}
