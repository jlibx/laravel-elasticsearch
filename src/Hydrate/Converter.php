<?php
declare(strict_types=1);

namespace Golly\Elastic\Hydrate;

/**
 * Class Converter
 * @package Golly\Elastic\Hydrate
 */
class Converter
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
