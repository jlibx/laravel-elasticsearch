<?php


namespace Golly\Elastic\DSL;

/**
 * Class Type
 * @package Golly\Elastic\DSL
 */
class Hydrate
{
    const TYPE_TINYINT = 'tinyint';
    const TYPE_SMALLINT = 'smallint';
    const TYPE_MEDIUMINT = 'mediumint';
    const TYPE_INTEGER = 'integer';
    const TYPE_BIGINT = 'bigint';
    const TYPE_CHAR = 'char';
    const TYPE_STRING = 'string';
    const TYPE_TINYTEXT = 'text';
    const TYPE_TEXT = 'text';
    const TYPE_FLOAT = 'float';
    const TYPE_DOUBLE = 'double';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_DATETIME = 'datetime';
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_TIME = 'time';
    const TYPE_DATE = 'date';
    const TYPE_YEAR = 'year';
    const TYPE_BINARY = 'binary';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_MONEY = 'money';
    const TYPE_JSON = 'json';

    /**
     * @param string $type
     * @return string
     */
    public static function toElasticType(string $type)
    {
        switch ($type) {
            case 'int':
            case 'integer':
            case 'timestamp':
                return 'integer';
            case 'real':
            case 'float':
            case 'double':
                return 'float';
            case 'string':
                return 'keyword';
            case 'bool':
            case 'boolean':
                return 'boolean';
            case 'year':
            case 'date':
            case 'datetime':
                return 'date';
            case 'object':
                return 'object';
            default:
                return $type;
        }
    }

    /**
     * @param string|null $format
     * @return string|null
     */
    public static function toElasticFormat(string $format = null)
    {
        switch ($format) {
            case 'Y':
                return 'yyyy';
            case 'Y-m-d':
                return 'yyyy-MM-dd';
            case 'Y-m-d H:i:s':
                return 'yyyy-MM-dd HH:mm:ss';
            default:
                return $format;
        }
    }
}
