<?php
declare(strict_types=1);

namespace Kabunx\Elastic\Hydrate;

use Kabunx\Elastic\Contracts\EsEntityInterface;
use Kabunx\Hydrate\Entity;

/**
 * 1、在使用的过程中，请避免使用联合类型声明，这样无法得到准确的数据类型
 * 2、如果一定要，请使用“mixed”声明
 * 3、如果需要将字段设置为text，请显示声明，否则将被处理为keyword
 * 4、数字类型，请显示声明，否则被处理为integer
 * 4、日期类型，请显示声明，否则被处理为||分隔的多格式4中格式的支持
 */
class EsEntity extends Entity implements EsEntityInterface
{
    /**
     * @var float|null
     */
    protected ?float $score = null;
    

    /**
     * @param array $data
     * @return $this
     */
    public function newInstance(array $data = []): static
    {
        return static::instance($data);
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return EsReflection::getProperties($this);
    }

    /**
     * @return float|null
     */
    public function getScore(): ?float
    {
        return $this->score;
    }

    /**
     * @param float|null $score
     * @return $this
     */
    public function setScore(?float $score): static
    {
        $this->score = $score;

        return $this;
    }

}
