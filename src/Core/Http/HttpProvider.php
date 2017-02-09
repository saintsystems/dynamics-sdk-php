<?php

namespace Microsoft\Core\Http;

use GuzzleHttp\Client;
use Microsoft\Dynamics\Constants;
use Microsoft\Dynamics\Http\DynamicsResponse;
use Microsoft\Dynamics\Exception\DynamicsException;

class HttpProvider implements IHttpProvider
{
    /**
     * Max Redirects
     *
     * @var  integer
     */
    const MAX_REDIRECTS = 5;

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
     * Sends the request.
     * @param  HttpRequestMessage $request The HttpRequestMessage to send.
     * @return HttpResponseMessage         The HttpResponseMessage.
     */
    // public function send(HttpRequestMessage $request)
    // {

    // }

    /**
    * Executes the HTTP request using Guzzle
    *
    * @param mixed $client The client to use in the request
    *
     * @throws DynamicsException if response is invalid
     *
    * @return mixed object or array of objects
    *         of class $returnType
    */
    public function send(HttpRequestMessage $request, $returnType = null)
    {
        // die(print_r($request, true));
        // \Microsoft\Dynamics\Core\Log::get_instance()->info(print_r($request, true));
        // die();
        $result = $this->http->request(
            $request->method, 
            $request->requestUri, 
            [
                'headers' => $request->headers,
                'body' => $request->content,
                'stream' =>  $request->returnsStream,
                'timeout' => $this->timeout
            ]
        );
        //die(print_r($result, true));

        //Send back the bare response
        if ($request->returnsStream) {
            return $result;
        }

        // Wrap response in DynamicsResponse layer
        try {
            $response = new DynamicsResponse(
                $this, 
                $result->getBody()->getContents(), 
                $result->getStatusCode(), 
                $result->getHeaders()
            );
        } catch (DynamicsException $e) {
            throw new DynamicsException(Constants::UNABLE_TO_PARSE_RESPONSE);
        }

        // If no return type is specified, return DynamicsResponse
        $returnObj = $response;

        //\Microsoft\Dynamics\Core\Log::get_instance()->info(print_r($response, true));
        //die();
        if ($returnType) {
            $returnObj = $response->getResponseAsObject($returnType);
        }
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
                $response = new DynamicsResponse(
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
