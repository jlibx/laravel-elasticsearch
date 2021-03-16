<?php


namespace Golly\Elastic\DSL\Queries;


/**
 * Class BoostingQuery
 * @package Golly\Elastic\DSL\Queries
 */
class BoostingQuery extends AbstractQuery
{

    /**
     * @var string
     */
    protected $type = 'boosting';

    protected $positive;

    protected $negative;

    protected $negativeBoost;

    public function __construct($positive, $negative, $negativeBoost)
    {
        $this->positive = $positive;
        $this->negative = $negative;
        $this->negativeBoost = $negativeBoost;
    }

    /**
     * @return array
     */
    public function output()
    {
        return [
            'positive' => $this->positive->toArray(),
            'negative' => $this->negative->toArray(),
            'negative_boost' => $this->negativeBoost,
        ];
    }
}
