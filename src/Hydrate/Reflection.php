<?php

namespace Golly\Elastic\Hydrate;

use Doctrine\Common\Annotations\AnnotationReader;
use Golly\Elastic\Contracts\EntityInterface;
use Golly\Elastic\Hydrate\Annotations\Mapping;
use Golly\Elastic\Hydrate\Annotations\Source;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionProperty;


/**
 * 字段映射，必须保证类名的唯一性
 *
 * Class Reflection
 * @package Golly\Elastic\Hydrate
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

    /**
     * 将数组赋值到对象
     *
     * @param array $data
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function hydrate(array $data, EntityInterface $entity)
    {
        $reflectProperties = $this->getReflectProperties($entity);
        $annotationReader = new AnnotationReader();
        foreach ($reflectProperties as $name => $property) {
            $defaultValue = $property->getValue($entity);
            $column = $annotationReader->getPropertyAnnotation($property, Source::class);
            if ($column instanceof Source) { // 映射关系
                $value = Arr::get($data, $column->field, $defaultValue);
            } else {
                $value = Arr::get($data, $name, $defaultValue);
            }
            $property->setValue($entity, $value);
        }

        return $entity;
    }


    /**
     * 对象转为数组
     *
     * @param $entity
     * @param string $format
     * @return array
     */
    public function extract(EntityInterface $entity, $format = 'snake')
    {
        $result = [];
        $properties = $this->getReflectProperties($entity);
        foreach ($properties as $name => $property) {
            $value = $property->getValue($entity);
            if (is_array($value)) {
                $arrValue = [];
                foreach ($value as $key => $item) {
                    if ($item instanceof EntityInterface) {
                        $arrValue[$key] = $this->extract($item, $format);
                    } else {
                        $arrValue[$key] = $item;
                    }
                }
                $value = $arrValue;
            } elseif ($value instanceof EntityInterface) {
                $value = $this->extract($value, $format);
            }
            // 格式转化
            switch ($format) {
                case 'camel':
                    $name = Str::camel($name);
                    break;
                case 'studly':
                    $name = Str::studly($name);
                    break;
                default:
                    $name = Str::snake($name);
                    break;
            }
            $result[$name] = $value;
        }

        return $result;
    }

    /**
     * 获取要转化对象的属性
     *
     * @param EntityInterface $entity
     * @return ReflectionProperty[]
     */
    protected function getReflectProperties(EntityInterface $entity)
    {
        $key = get_class($entity);
        if (isset(static::$reflectProperties[$key])) {
            return static::$reflectProperties[$key];
        }
        // 获取”public“属性
        $reflectProperties = (new ReflectionClass($entity))->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($reflectProperties as $property) {
            $property->setAccessible(true);
            static::$reflectProperties[$key][$property->getName()] = $property;
        }

        return static::$reflectProperties[$key];
    }
}
