<?php


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
    protected $script;

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
    public function getType()
    {
        return 'script';
    }

    /**
     * @return array
     */
    public function getTypeValue()
    {
        return [
            'script' => $this->merge([
                'inline' => $this->script
            ])
        ];
    }

}
