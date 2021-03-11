<?php


namespace Golly\Elastic\DSL;

use Golly\Elastic\Contracts\HighlightInterface;
use stdClass;

/**
 * Class Highlight
 * @package Golly\Elastic\DSL
 */
class Highlight implements HighlightInterface
{
    use HasParams;

    /**
     * @var string
     */
    protected $type = 'highlight';

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * @var array
     */
    protected $fields = [];


    /**
     * @param array $preTags
     * @return $this
     */
    public function setPreTags(array $preTags)
    {
        $this->tags['pre_tags'] = $preTags;

        return $this;
    }

    /**
     * @param array $postTags
     * @return $this
     */
    public function setPostTags(array $postTags)
    {
        $this->tags['post_tags'] = $postTags;

        return $this;
    }

    /**
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function addField(string $name, array $params = [])
    {
        $this->fields[$name] = $params;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $output = [];

        if (is_array($this->tags)) {
            $output = $this->tags;
        }

        $output = $this->merge($output);

        foreach ($this->fields as $field => $params) {
            $output['fields'][$field] = $params ?? new stdClass();
        }

        return $output;
    }
}
