<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries\FullText;

use Golly\Elastic\Queries\Query;

/**
 * Class MatchQuery
 * @package Golly\Elastic\Queries\FullText
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html
 */
class MatchQuery extends Query
{

    /**
     * MatchQuery constructor.
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
        return 'match';
    }

    /**
     * @return array
     */
    public function getTypeValue(): array
    {
        return [
            $this->field => $this->merge([
                'query' => $this->value
            ]),
        ];
    }
}
