<?php
declare(strict_types=1);

namespace Golly\Elastic\Aggregations\Bucket;


/**
 * Class RangeAggregation
 * @package Golly\Elastic\Aggregations\Bucket
 */
class RangeAggregation extends BucketAggregation
{
    /**
     * @var string
     */
    protected string $type = 'range';

    /**
     * @var bool
     */
    protected bool $keyed = false;

    /**
     * @var array
     */
    protected array $ranges = [];

    /**
     * RangeBucket constructor.
     * @param string $field
     * @param array $ranges
     * @param false $keyed
     */
    public function __construct(string $field, array $ranges = [], bool $keyed = false)
    {
        parent::__construct($field);

        $this->keyed = $keyed;
        $this->handleRange($ranges);
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        $data = [
            'keyed' => $this->keyed,
            'ranges' => array_values($this->ranges),
        ];

        if ($this->field) {
            $data['field'] = $this->field;
        }

        return $data;
    }


    /**
     * @param array $ranges
     * @return void
     */
    public function handleRange(array $ranges): void
    {
        foreach ($ranges as $range) {
            if (!is_array($range)) {
                continue;
            }
            $from = $range['from'] ?? null;
            $to = $range['to'] ?? null;
            $key = $range['key'] ?? null;
            $this->addRange($from, $to, $key);
        }
    }


    /**
     * @param mixed $from
     * @param mixed $to
     * @param mixed $key
     * @return $this
     */
    public function addRange(mixed $from = null, mixed $to = null, mixed $key = null): self
    {
        $range = array_filter([
            'from' => $from,
            'to' => $to,
        ], function ($v) {
            return !is_null($v);
        });

        if ($key) {
            $range['key'] = $key;
        }

        $this->ranges[] = $range;

        return $this;
    }
}
