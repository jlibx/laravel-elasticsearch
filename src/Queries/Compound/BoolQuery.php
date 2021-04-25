<?php


namespace Golly\Elastic\Queries\Compound;

use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\Queries\Query;
use stdClass;

/**
 * Class BoolQuery
 * @package Golly\Elastic\Queries\Compound
 */
class BoolQuery extends Query
{
    const MUST = 'must';  // 与 AND 等价。
    const MUST_NOT = 'must_not'; // 与 NOT 等价
    const SHOULD = 'should'; // 与 OR 等价
    const FILTER = 'filter';

    /**
     * @var array
     */
    public $wheres = [];


    /**
     * BooleanQuery constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'bool';
    }

    /**
     * @return array|stdClass
     */
    public function getTypeValue()
    {
        $output = [];
        foreach ($this->wheres as $type => $queries) {
            /** @var QueryInterface $query */
            foreach ($queries as $query) {
                $output[$type][] = $query->toArray();
            }
        }
        $output = $this->merge($output);

        if (empty($output)) {
            $output = new stdClass();
        }

        return $output;
    }

    /**
     * @param QueryInterface $query
     * @param $type
     */
    public function addQuery(QueryInterface $query, $type)
    {
        if ($type == self::SHOULD) {
            $this->addParam('minimum_should_match', 1);
        }
        $this->wheres[$type][] = $query;
    }
}
