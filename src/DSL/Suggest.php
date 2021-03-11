<?php


namespace Golly\Elastic\DSL;


use Golly\Elastic\Contracts\SuggestInterface;

/**
 * Class Suggest
 * @package Golly\Elastic\DSL
 */
class Suggest implements SuggestInterface
{
    use HasParams;


    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $field;


    /**
     * Suggest constructor.
     * @param string $name
     * @param string $type
     * @param string $text
     * @param string $field
     * @param array $params
     */
    public function __construct(
        string $name,
        string $type,
        string $text,
        string $field,
        array $params = []
    )
    {
        $this->name = $name;
        $this->type = $type;
        $this->text = $text;
        $this->field = $field;
        $this->setParams($params);
    }


    /**
     * @return array
     */
    public function toArray()
    {
        return [
            $this->name => [
                'text' => $this->text,
                $this->type => $this->merge(['field' => $this->field]),
            ]
        ];
    }
}
