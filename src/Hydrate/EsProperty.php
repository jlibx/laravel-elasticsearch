<?php
declare(strict_types=1);

namespace Kabunx\Elastic\Hydrate;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EsProperty
{
    /**
     * 数据类型
     */
    public string $type;

    /**
     * 数据格式
     */
    public ?string $format;

    /**
     * 分词器
     */
    public ?string $analyzer;

    /**
     * @param string $type
     * @param string|null $format
     * @param string|null $analyzer
     */
    public function __construct(string $type, ?string $format = null, ?string $analyzer = null,)
    {
        $this->type = $type;
        $this->format = $format;
        $this->analyzer = $analyzer;
    }
}
