<?php

namespace Microsoft\OData;

use GuzzleHttp\Client;

class HttpProvider implements IHttpProvider
{
    /**
    * The Guzzle client used to make the HTTP request
    *
    * @var Client
    */
    protected $http;

    /**
    * The timeout, in seconds
    *
    * @var string
    */
    protected $timeout;

    /**
     * Creates a new HttpProvider
     */
    public function __construct()
    {
        $this->http = new Client();
        $this->timeout = 0;
    }

    /**
     * Gets the timeout limit of the cURL request
     * @return integer  The timeout in ms
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Sets the timeout limit of the cURL request
     *
     * @param integer $timeout The timeout in ms
     * 
     * @return HttpProvider object
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
    * Executes the HTTP request using Guzzle
    *
    * @param mixed $client The client to use in the request
    *
     * @throws ODataException if response is invalid
     *
    * @return mixed object or array of objects
    *         of class $returnType
    */
    public function send(HttpRequestMessage $request, $returnType = null)
    {
        $returnType = is_null($returnType) ? Entity::class : $returnType;

        $result = $this->http->request(
            $request->method->value(), 
            $request->requestUri, 
            [
                'headers' => $request->headers,
                'body' => $request->content,
                'stream' =>  $request->returnsStream,
                'timeout' => $this->timeout
            ]
        );

        //Send back the bare response
        if ($request->returnsStream) {
            return $result;
        }

        // Wrap response in ODataResponse layer
        try {
            $response = new ODataResponse(
                $this, 
                $result->getBody()->getContents(), 
                $result->getStatusCode(), 
                $result->getHeaders()
            );
        } catch (ODataException $e) {
            throw new ODataException(Constants::UNABLE_TO_PARSE_RESPONSE);
        }

        // If no return type is specified, return ODataResponse
        $returnObj = $response;

        $returnObj = $response->getResponseAsObject($returnType);
        dd($returnObj);
        return $returnObj; 
    }

    /**
    * Executes the HTTP request asynchronously using Guzzle
    *
    * @param mixed $client The client to use in the request
    *
    * @return mixed object or array of objects
    *         of class $returnType
    */
    public function sendAsync($client = null)
    {
        if (is_null($client)) {
            $client = $this->createGuzzleClient();
        }

        $promise = $client->requestAsync(
            $this->requestType,
            $this->getRequestUrl(),
            [
                'body' => $this->requestBody,
                'stream' => $this->returnsStream,
                'timeout' => $this->timeout
            ]
        )->then(
            // On success, return the result/response
            function ($result) {
                $response = new ODataResponse(
                    $this, 
                    $result->getBody()->getContents(), 
                    $result->getStatusCode(), 
                    $result->getHeaders()
                );
                $returnObject = $response;
                if ($this->returnType) {
                    $returnObject = $response->getResponseAsObject(
                        $this->returnType
                    );
                }
                return $returnObject;
            },
            // On fail, log the error and return null
            function ($reason) {
                trigger_error("Async call failed: " . $reason->getMessage());
                return null;
            }
        );
        return $promise;
    }

}
