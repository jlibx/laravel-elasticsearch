<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries;

/**
 * Class MatchAllQuery
 * @package Golly\Elastic\Queries
 */
class MatchAllQuery extends Query
{

    /**
     * MatchAllQuery constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'match_all';
    }

    /**
     * @return array
     */
    public function getTypeValue(): array
    {
        return $this->params;
    }

}
