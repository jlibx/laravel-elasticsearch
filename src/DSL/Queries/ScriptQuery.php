<?php


namespace Golly\Elastic\DSL\Queries;

/**
 * Class ScriptQuery
 * @package Golly\Elastic\DSL\Queries
 */
class ScriptQuery extends AbstractQuery
{

    /**
     * @var string
     */
    protected $type = 'script';


    /**
     * ScriptQuery constructor.
     * @param string $script
     * @param array $params
     */
    public function __construct(string $script, array $params = [])
    {
        $this->value = $script;
        $this->setParams($params);
    }

    /**
     * @return array
     */
    public function output()
    {
        $output = $this->merge([
            'inline' => $this->value
        ]);

        return [
            'script' => $output
        ];
    }
}
