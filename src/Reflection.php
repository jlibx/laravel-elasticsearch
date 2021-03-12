<?php


namespace Golly\Elastic;

use Doctrine\Common\Annotations\AnnotationReader;
use Golly\Elastic\Annotations\Mapping;
use Golly\Elastic\Annotations\Source;
use Golly\Elastic\Contracts\EntityInterface;
use Golly\Elastic\DSL\Hydrate;
use Golly\Elastic\Exceptions\ElasticException;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionProperty;
use Throwable;

/**
 * Class Reflection
 * @package Golly\Elastic
 */
class Reflection
{
    /**
     * @var array
     */
    protected static $reflectProperties = [];


    /**
     * @param EntityInterface $entity
     * @return array
     * @throws ElasticException
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
                        'type' => Hydrate::toElasticType($mapping->type),
                        'analyzer' => $mapping->analyzer,
                        'format' => Hydrate::toElasticFormat($mapping->format)
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

    /**
     * @param array $data
     * @param EntityInterface $entity
     * @return EntityInterface
     * @throws ElasticException
     */
    public function hydrate(array $data, EntityInterface $entity)
    {
        $reflectProperties = $this->getReflectProperties($entity);
        $annotationReader = new AnnotationReader();
        foreach ($reflectProperties as $name => $property) {
            $column = $annotationReader->getPropertyAnnotation($property, Source::class);
            $defaultValue = $property->getValue($entity);
            if ($column instanceof Source) { // 映射关系
                $value = Arr::get($data, $column->field, $defaultValue);
            } else {
                $value = Arr::get($data, $name, $defaultValue);
            }
            $reflectProperties[$name]->setValue($entity, $value);
        }

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @param bool $snake
     * @return array
     * @throws ElasticException
     */
    public function extract(EntityInterface $entity, $snake = true)
    {
        $result = [];
        $properties = self::getReflectProperties($entity);
        foreach ($properties as $name => $property) {
            $value = $property->getValue($entity);
            if (is_array($value)) {
                $arrValue = [];
                foreach ($value as $key => $item) {
                    if ($item instanceof EntityInterface) {
                        $arrValue[$key] = $this->extract($item);
                    } else {
                        $arrValue[$key] = $item;
                    }
                }
                $value = $arrValue;
            } elseif ($value instanceof EntityInterface) {
                $value = $this->extract($value);
            }
            if ($snake) {
                $name = preg_replace('/\s+/u', '', ucwords($name));
                $name = mb_strtolower((preg_replace('/(.)(?=[A-Z])/u', '$1_', $name)), 'UTF-8');
            }

            $result[$name] = $value;
        }

        return $result;
    }

    /**
     * @param EntityInterface $entity
     * @return ReflectionProperty[]
     * @throws ElasticException
     */
    protected function getReflectProperties(EntityInterface $entity)
    {
        $key = get_class($entity);
        if (isset(static::$reflectProperties[$key])) {
            return static::$reflectProperties[$key];
        }
        try {
            $reflectProperties = (new ReflectionClass($entity))->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($reflectProperties as $property) {
                $property->setAccessible(true);
                static::$reflectProperties[$key][$property->getName()] = $property;
            }

            return static::$reflectProperties[$key];
        } catch (Throwable $e) {
            throw new ElasticException('Input must be an entity.');
        }
    }
}
