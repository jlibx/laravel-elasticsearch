<?php
declare(strict_types=1);

namespace Golly\Elastic\Queries\Specialized;

use Golly\Elastic\Queries\Query;

/**
 * Class ScriptQuery
 * @package Golly\Elastic\Queries\Specialized
 */
class ScriptQuery extends Query
{

    /**
     * @var string
     */
    protected string $script;

    /**
     * @param string $script
     * @param array $params
     */
    public function __construct(string $script, array $params = [])
    {
        $this->script = $script;
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'script';
    }

    /**
     * @return array
     */
    public function getTypeValue(): array
    {
        return [
            'script' => $this->merge([
                'inline' => $this->script
            ])
        ];
    }

}
