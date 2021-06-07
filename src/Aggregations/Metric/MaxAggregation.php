<?php
declare(strict_types=1);

namespace Golly\Elastic\Aggregations\Metric;


/**
 * Class MaxAggregation
 * @package Golly\Elastic\Aggregations\Metric
 */
class MaxAggregation extends StatsAggregation
{
    /**
     * @var string
     */
    protected string $type = 'max';
}
