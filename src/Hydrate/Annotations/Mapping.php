<?php


namespace Golly\Elastic\Hydrate\Annotations;

/**
 * Class Mapping
 * @package Golly\Elastic\Hydrate\Annotations
 * @Annotation
 */
final class Mapping
{

    /**
     * @var string
     */
    public $type;

    /**
     * analyzer of attribute
     *
     * @var string
     */
    public $analyzer;

    /**
     * format of date
     *
     * @var string
     */
    public $format;

}
