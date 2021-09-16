<?php
declare(strict_types=1);

namespace Kabunx\LaravelElasticsearch\Hydrate;

use ReflectionProperty;

class EsPropertyReader
{

    public function getEsType(ReflectionProperty $property): string
    {
        $ea = $this->getEsProperty($property);

        return $ea->type;
    }

    public function getEsFormat(ReflectionProperty $property): ?string
    {
        $ea = $this->getEsProperty($property);

        return $ea?->format;
    }

    public function getEsAnalyzer(ReflectionProperty $property): ?string
    {
        $ea = $this->getEsProperty($property);

        return $ea?->analyzer;
    }

    public function getEsProperty(ReflectionProperty $property): ?EsProperty
    {
        $attrs = $property->getAttributes(EsProperty::class);
        if (count($attrs) > 0) {
            foreach ($attrs as $attr) {
                $esAttribute = $attr->newInstance();
                if ($esAttribute instanceof EsProperty) {
                    return $esAttribute;
                }
            }
        }

        return null;
    }
}
