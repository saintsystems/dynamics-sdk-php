<?php

namespace Microsoft\Dynamics\Http;

/**
 * The base request builder class.
 */
class BaseRequestBuilder
{
    /**
     * Constructs a new BaseRequestBuilder.
     * @param string      $requestUrl The URL for the built request.
     * @param IBaseClient $client     The IBaseClient for handling requests.
     */
    public function __construct($requestUrl, IBaseClient $client, $returnType)
    {
        $this->client = $client;
        $this->requestUrl = $requestUrl;
        $this->returnType = $returnType;
    }

    /**
     * Gets the IBaseClient for handling requests.
     * @var IBaseClient
     */
    public $client;

    /**
     * Gets the URL for the built request, without query string.
     * @var string
     */
    public $requestUrl;

    /**
     * Gets the URL for the built request, without query string.
     * @var object
     */
    public $returnType;

    /**
     * Gets a URL that is the request builder's request URL with the segment appended.
     * @param  string $urlSegment The segment to append to the request URL.
     * @return string             A URL that is the request builder's request URL with the segment appended.
     */
    public function appendSegmentToRequestUrl($urlSegment) //:string
    {
        return sprintf('%s/%s', $this->requestUrl, $urlSegment);
    }
}
