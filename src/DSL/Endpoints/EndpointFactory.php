<?php


namespace Golly\Elastic\DSL\Endpoints;

/**
 * Class EndpointFactory
 * @package Golly\Elastic\DSL\Endpoints
 */
class EndpointFactory
{

    /**
     * @var string[]
     */
    public static $endpoints = [
        'query' => QueryEndpoint::class,
        'sort' => SortEndpoint::class,
        'aggregations' => AggregationEndpoint::class,
        'highlight' => HighlightEndpoint::class,
        'suggest' => SuggestEndpoint::class,
        'inner_hits' => InnerHitEndpoint::class
    ];

    /**
     * @param string $type
     * @return AbstractEndpoint|null
     */
    public static function get(string $type)
    {
        if (isset(self::$endpoints[$type])) {
            return new self::$endpoints[$type];
        }

        return null;
    }
}
