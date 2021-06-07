<?php
declare(strict_types=1);

namespace Golly\Elastic\Aggregations\Bucket;

/**
 * Class TermsAggregation
 * @package Golly\Elastic\Aggregations\Buckets
 */
class TermsAggregation extends BucketAggregation
{
    /**
     * @var string
     */
    protected string $type = 'terms';

    /**
     * TermsBucket constructor.
     * @param string $field
     * @param array $script
     */
    public function __construct(string $field, array $script = [])
    {
        parent::__construct($field);

        $this->field = $field;
        $this->scripts = $script;
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        return array_filter([
            'field' => $this->field,
            'script' => $this->scripts,
        ]);
    }

}
