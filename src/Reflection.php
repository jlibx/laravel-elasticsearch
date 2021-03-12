<?php


namespace Golly\Elastic;

use Doctrine\Common\Annotations\AnnotationReader;
use Golly\Elastic\Annotations\Mapping;
use Golly\Elastic\Annotations\Source;
use Golly\Elastic\Contracts\EntityInterface;
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
     * @return EntityInterface
     * @throws ElasticException
     */
    public function map(EntityInterface $entity)
    {
        $reflectProperties = $this->getReflectProperties($entity);
        $annotationReader = new AnnotationReader();
        foreach ($reflectProperties as $name => $property) {
            $column = $annotationReader->getPropertyAnnotation($property, Mapping::class);
            if ($column instanceof Mapping) {
                if ($column->type == 'array') {
                    $mapping = [];
                } else {
                    $mapping = array_filter([
                        'type' => $column->type,
                        'analyzer' => $column->analyzer,
                        'format' => $column->format
                    ]);
                }
            } else {
                $mapping = [
                    'type' => 'text'
                ];
            }
            $reflectProperties[$name]->setValue($entity, $mapping);
        }

        return $entity;
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
     * @return array
     * @throws ElasticException
     */
    public function extract(EntityInterface $entity)
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
