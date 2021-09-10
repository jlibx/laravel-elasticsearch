<?php
declare(strict_types=1);

namespace Kabunx\Elastic\Hydrate;

use Kabunx\Elastic\Hydrate\Annotations\EsProperty;
use Kabunx\Elastic\EsMapper;
use Kabunx\Hydrate\Annotations\Source;
use Kabunx\Hydrate\Contracts\EntityInterface;
use Kabunx\Hydrate\Reflection;

class EsReflection extends Reflection
{
    /**
     * @param EntityInterface $entity
     * @return array
     */
    public static function getProperties(EntityInterface $entity): array
    {
        $instance = (new static());
        $instance->entity = $entity;
        $reflectProperties = $instance->getReflectionProperties();
        $properties = [];
        foreach ($reflectProperties as $name => $property) {
            $field = $instance->toSnakeName($name);
            $esProperty = $instance->getAnnotationReader()->getPropertyAnnotation($property, EsProperty::class);
            if ($esProperty instanceof EsProperty && $esProperty->type) {
                $value = EsMapper::fromEsProperty($esProperty);
                $source = $instance->getAnnotationReader()->getPropertyAnnotation($property, Source::class);
                if ($source instanceof Source) {
                    $field = $source->from;
                }
            } else {
                $value = EsMapper::fromReflectionType($property->getType());
            }
            if ($value) {
                $properties[$field] = $value;
            }
        }

        return $properties;
    }
}
