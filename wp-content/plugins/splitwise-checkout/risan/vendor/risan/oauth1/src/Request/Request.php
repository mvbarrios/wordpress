<?php

namespace Risan\OAuth1\Request;

class Request implements RequestInterface
{
    /**
     * The request HTTP method.
     *
     * @var string
     */
    protected $method;

    /**
     * The request URI.
     *
     * @var string
     */
    protected $uri;

    /**
     * The request options.
     *
     * @var array
     */
    protected $options;

    /**
     * Create a new instance of Request class.
     *
     * @param string $method
     * @param string $uri
     * @param array  $options
     */
    public function __construct($method, $uri, $options = [])
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }
}
