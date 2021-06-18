<?php
declare(strict_types=1);

namespace Golly\Elastic\Hydrate;

/**
 * Class Converter
 * @package Golly\Elastic\Hydrate
 */
class Converter
{
    public const TYPE_TINYINT = 'tinyint';
    public const TYPE_SMALLINT = 'smallint';
    public const TYPE_MEDIUMINT = 'mediumint';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_BIGINT = 'bigint';
    public const TYPE_CHAR = 'char';
    public const TYPE_STRING = 'string';
    public const TYPE_TINYTEXT = 'text';
    public const TYPE_TEXT = 'text';
    public const TYPE_FLOAT = 'float';
    public const TYPE_DOUBLE = 'double';
    public const TYPE_DECIMAL = 'decimal';
    public const TYPE_DATETIME = 'datetime';
    public const TYPE_TIMESTAMP = 'timestamp';
    public const TYPE_TIME = 'time';
    public const TYPE_DATE = 'date';
    public const TYPE_YEAR = 'year';
    public const TYPE_BINARY = 'binary';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_MONEY = 'money';
    public const TYPE_JSON = 'json';
    public const IK_SMART = 'ik_smart';

    /**
     * @var string[]
     */
    protected static array $analyzers = [
        'ik_smart',
        'ik_max_word'
    ];

    /**
     * @param string|null $type
     * @return string
     */
    public static function toElasticType(?string $type): string
    {
        return match ($type) {
            'int', 'integer', 'timestamp' => 'integer',
            'real', 'float', 'double' => 'float',
            'string' => 'keyword',
            'bool', 'boolean' => 'boolean',
            'year', 'date', 'datetime' => 'date',
            'object' => 'object',
            default => 'text',
        };
    }

    /**
     * @param string|null $format
     * @return string|null
     */
    public static function toElasticFormat(?string $format): ?string
    {
        return match ($format) {
            'Y' => 'yyyy',
            'Y-m-d' => 'yyyy-MM-dd',
            'Y-m-d H:i:s' => 'yyyy-MM-dd HH:mm:ss',
            default => null,
        };
    }

    /**
     * @param string|null $analyzer
     * @return string|null
     */
    public static function toElasticAnalyzer(?string $analyzer): ?string
    {
        if (is_null($analyzer)) {
            return null;
        }
        return in_array($analyzer, self::$analyzers) ? $analyzer : null;
    }
}
