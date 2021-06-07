<?php
declare(strict_types=1);

namespace Golly\Elastic\Hydrate;

use Doctrine\Common\Annotations\AnnotationReader;
use Golly\Elastic\Hydrate\Annotations\Mapping;
use Golly\Hydrate\Annotations\Source;
use Golly\Hydrate\Contracts\EntityInterface;
use Golly\Hydrate\Reflection as HydrateReflection;

/**
 *
 * Class Reflection
 * @package Golly\Elastic\Hydrate
 */
class Reflection extends HydrateReflection
{

    /**
     * @param EntityInterface $entity
     * @return array
     */
    public static function mapping(EntityInterface $entity): array
    {
        $properties = parent::getReflectProperties($entity);
        $reader = new AnnotationReader();
        $result = [];
        foreach ($properties as $name => $property) {
            $mapping = $reader->getPropertyAnnotation($property, Mapping::class);
            $source = $reader->getPropertyAnnotation($property, Source::class);
            if ($mapping instanceof Mapping) {
                if ($mapping->type == 'relation') {
                    continue;
                } else {
                    $value = array_filter([
                        'type' => Converter::toElasticType($mapping->type),
                        'analyzer' => Converter::toElasticAnalyzer($mapping->analyzer),
                        'format' => Converter::toElasticFormat($mapping->format)
                    ]);
                }
            } else {
                $value = ['type' => 'text'];
            }
            if ($source instanceof Source) {
                $name = $source->field;
            }
            $result[$name] = $value;
        }

        return $result;
    }

}
