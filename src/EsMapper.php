<?php
declare(strict_types=1);

namespace Kabunx\Elastic;

use Carbon\Carbon;
use Kabunx\Elastic\Contracts\EsEntityInterface;
use Kabunx\Elastic\Hydrate\Annotations\EsProperty;
use ReflectionNamedType;
use ReflectionType;

/**
 * 1、字符串类型： 文本类型 - text 关键字类型 - keyword
 * 2、数字类型 - 8种
 *      byte    有符号的8位整数, 范围: [-128 ~ 127]
 *      short    有符号的16位整数, 范围: [-32768 ~ 32767]
 *      integer    有符号的32位整数, 范围: [−231 ~ 231-1]
 *      long    有符号的64位整数, 范围: [−263 ~ 263-1]
 *      float    32位单精度浮点数
 *      double    64位双精度浮点数
 * 3、日期类型 - date
 *      包含格式化日期的字符串, "2018-10-01", 或"2018/10/01 12:10:30".
 *      代表时间毫秒数的长整型数字.
 *      代表时间秒数的整数.
 * 4、布尔类型 - boolean
 *      真值: true, "true", "on", "yes", "1"...
 *      假值: false, "false", "off", "no", "0", ""(空字符串), 0.0, 0
 * 5、范围类型 - range
 *      integer_range	−231 ~ 231−1
 *      long_range	−263 ~ 263−1
 *      float_range	32位单精度浮点型
 *      double_range	64位双精度浮点型
 *      date_range	64位整数, 毫秒计时
 *      ip_range	IP值的范围, 支持IPV4和IPV6, 或者这两种同时存在
 * 6、对象类型 - object
 * 7、嵌套类型 - nested
 */
class EsMapper
{

    /**
     * @var string[]
     */
    protected static array $analyzers = ['ik_smart', 'ik_max_word'];

    /**
     * @param EsProperty $property
     * @return array
     */
    public static function fromEsProperty(EsProperty $property): array
    {
        if (class_exists($property->type)) {
            return static::toClassProperties($property->type);
        }
        return array_filter([
            'type' => static::toType($property->type),
            'format' => static::toFormat($property->format),
            'analyzer' => static::toAnalyzer($property->analyzer)
        ]);
    }

    /**
     * @param ReflectionType|null $reflectionType
     * @return array
     */
    public static function fromReflectionType(ReflectionType $reflectionType = null): array
    {
        $property = [];
        if ($reflectionType instanceof ReflectionNamedType) {
            $name = $reflectionType->getName();
            if (class_exists($name)) {
                $property = static::toClassProperties($name);
            } else {
                $property = [
                    'type' => static::toType($name)
                ];
            }
        }

        return array_filter($property);
    }

    /**
     * @param string $type
     * @return string
     */
    public static function toType(string $type): string
    {
        return match ($type) {
            'int', 'integer', 'timestamp' => 'integer',
            'real', 'float', 'double' => 'float',
            'string' => 'keyword',
            'bool', 'boolean' => 'boolean',
            'year', 'date', 'datetime' => 'date',
            'object' => 'object',
            'text' => 'text',
            default => ''
        };
    }

    /**
     * @param string $format
     * @return string
     */
    public static function toFormat(string $format): string
    {
        return match ($format) {
            'Y', 'year' => 'yyyy',
            'Y-m-d', 'date' => 'yyyy-MM-dd',
            'Y-m-d H:i:s', 'datetime' => 'yyyy-MM-dd HH:mm:ss',
            default => $format,
        };
    }

    /**
     * @param string $analyzer
     * @return string
     */
    public static function toAnalyzer(string $analyzer): string
    {
        return in_array($analyzer, self::$analyzers) ? $analyzer : '';
    }

    /**
     * @param string $classname
     * @return array
     */
    public static function toClassProperties(string $classname): array
    {
        $class = new $classname;
        if ($class instanceof Carbon) {
            return [
                'type' => 'date',
                'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_second||epoch_millis'
            ];
        } elseif ($class instanceof EsEntityInterface) {
            return [
                'properties' => $class->getProperties()
            ];
        }
        return [];
    }
}
