<?php

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
    public function map(EntityInterface $entity)
    {
        $reflectProperties = $this->getReflectProperties($entity);
        $annotationReader = new AnnotationReader();
        $result = [];
        foreach ($reflectProperties as $name => $property) {
            $mapping = $annotationReader->getPropertyAnnotation($property, Mapping::class);
            $source = $annotationReader->getPropertyAnnotation($property, Source::class);
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
