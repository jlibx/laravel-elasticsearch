<?php


namespace Golly\Elastic\Annotations;


/**
 * Class Mapping
 * @package Golly\Elastic\Annotations
 * @Annotation
 */
final class Mapping
{

    /**
     * type of attribute
     *
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
