<?php
declare(strict_types=1);

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
    public string $type;

    /**
     * analyzer of attribute
     *
     * @var string
     */
    public string $analyzer;

    /**
     * format of date
     *
     * @var string
     */
    public string $format;

}
