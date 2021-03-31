<?php


namespace Golly\Elastic\Queries\Joining;


use Golly\Elastic\Queries\Query;

/**
 * Class ParentIdQuery
 * @package Golly\Elastic\Queries\Joining
 */
class ParentIdQuery extends Query
{

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;


    /**
     * @param $id
     * @param string $type
     * @param array $params
     */
    public function __construct($id, string $type, array $params = [])
    {
        $this->id = $id;
        $this->type = $type;
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'parent_id';
    }

    /**
     * @return array
     */
    public function getOutput()
    {
        return $this->merge([
            'id' => $this->id,
            'type' => $this->type,
        ]);
    }


}
