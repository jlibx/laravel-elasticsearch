<?php
declare(strict_types=1);

namespace Golly\Elastic\Endpoints;

use stdClass;

/**
 * Class HighlightEndpoint
 * @package Golly\Elastic\Endpoints
 */
class HighlightEndpoint extends Endpoint
{

    /**
     * @var array
     */
    protected array $fields = [];

    /**
     * @var array
     */
    protected array $tags = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'highlight';
    }

    /**
     * @return array
     */
    public function normalize(): array
    {
        $output = [];
        if (is_array($this->tags)) {
            $output = $this->tags;
        }
        $output = $this->merge($output);
        foreach ($this->fields as $field => $params) {
            $output['fields'][$field] = count($params) ? $params : new stdClass();
        }

        return $output;
    }

    /**
     * @param string $name Field name to highlight.
     * @param array $params
     *
     * @return $this
     */
    public function addField(string $name, array $params = []): static
    {
        $this->fields[$name] = $params;

        return $this;
    }

}
