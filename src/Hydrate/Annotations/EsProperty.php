<?php
declare(strict_types=1);

namespace Kabunx\Elastic\Hydrate\Annotations;

/**
 * es mapping property 类型定义
 *
 * @Annotation
 */
class EsProperty
{
    /**
     * @var string
     */
    public string $type = '';

    /**
     * @var string
     */
    public string $analyzer = '';

    /**
     * @var string
     */
    public string $format = '';
}
