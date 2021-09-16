<?php
declare(strict_types=1);

namespace Kabunx\Elastic\Hydrate;

use Kabunx\Hydrate\Contracts\EntityInterface;
use Kabunx\Hydrate\Hydrate;

class EsHydrate extends Hydrate
{

    protected static EsPropertyReader $esPropertyReader;

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
            $field = $instance->getSourceKeyName($name);
            $esProperty = $instance->getEsPropertyReader()->getEsProperty($property);
            if ($esProperty) {
                $value = EsMapper::fromEsProperty($esProperty);
                $from = $instance->getAttributeReader()->getColumnFrom($property);
                if ($from) {
                    $field = $from;
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


    protected function getEsPropertyReader(): EsPropertyReader
    {
        if (! isset(static::$esPropertyReader)) {
            static::$esPropertyReader = new EsPropertyReader();
        }
        return static::$esPropertyReader;
    }
}
