<?php


namespace Golly\Elastic\DSL\Aggregations\Buckets;


/**
 * Class RangeBucket
 * @package Golly\Elastic\DSL\Aggregations\Buckets
 */
class RangeBucket extends Bucket
{
    /**
     * @var string
     */
    protected $type = 'range';

    /**
     * @var bool
     */
    protected $keyed = false;

    /**
     * @var array
     */
    protected $ranges = [];

    /**
     * RangeBucket constructor.
     * @param string $field
     * @param array $ranges
     * @param false $keyed
     */
    public function __construct(string $field, array $ranges = [], $keyed = false)
    {
        parent::__construct($field);

        $this->keyed = $keyed;
        $this->handleRange($ranges);
    }

    /**
     * @return array
     */
    public function getArray()
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
     */
    public function handleRange(array $ranges)
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
     * @param null $from
     * @param null $to
     * @param string $key
     * @return $this
     */
    public function addRange($from = null, $to = null, $key = null)
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
