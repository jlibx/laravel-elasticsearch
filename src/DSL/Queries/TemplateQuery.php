<?php


namespace Golly\Elastic\DSL\Queries;


use Golly\Elastic\Exceptions\ElasticQueryException;

/**
 * Class TemplateQuery
 * @package Golly\Elastic\DSL\Queries
 */
class TemplateQuery extends AbstractQuery
{

    protected $type = 'template';

    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $inline;

    /**
     * TemplateQuery constructor.
     * @param string|null $file
     * @param string|null $inline
     * @param array $params
     */
    public function __construct(string $file = null, string $inline = null, array $params = [])
    {
        $this->setFile($file);
        $this->setInline($inline);
        $this->setParams($params);
    }

    /**
     * @return array
     * @throws ElasticQueryException
     */
    public function output()
    {
        $output = array_filter(
            [
                'file' => $this->file,
                'inline' => $this->inline,
                'params' => $this->params,
            ]
        );

        if (!isset($output['file']) && !isset($output['inline'])) {
            throw new ElasticQueryException(
                'Template query requires that either `inline` or `file` parameters are set'
            );
        }

        return $this->merge($output);
    }

    /**
     * @param mixed $file
     */
    public function setFile($file): void
    {
        $this->file = $file;
    }

    /**
     * @param mixed $inline
     */
    public function setInline($inline): void
    {
        $this->inline = $inline;
    }

}
