<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries\Compound;

use Golly\Elastic\Contracts\QueryInterface;
use Golly\Elastic\Queries\Query;

/**
 * Class BoostingQuery
 * @package Golly\Elastic\Queries\Compound
 */
class BoostingQuery extends Query
{

    /**
     * @var QueryInterface
     */
    protected QueryInterface $positive;

    /**
     * @var QueryInterface
     */
    protected QueryInterface $negative;

    /**
     * @var mixed
     */
    protected mixed $negativeBoost;

    /**
     * BoostingQuery constructor.
     * @param QueryInterface $positive
     * @param QueryInterface $negative
     * @param mixed $negativeBoost
     */
    public function __construct(
        QueryInterface $positive,
        QueryInterface $negative,
        mixed $negativeBoost
    )
    {
        $this->positive = $positive;
        $this->negative = $negative;
        $this->negativeBoost = $negativeBoost;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'boosting';
    }

    /**
     * @return array
     */
    public function getTypeValue(): array
    {
        return [
            'positive' => $this->positive->toArray(),
            'negative' => $this->negative->toArray(),
            'negative_boost' => $this->negativeBoost,
        ];
    }
}
