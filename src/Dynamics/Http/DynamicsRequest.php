<?php 
/**
* Copyright (c) Saint Systems, LLC.  All Rights Reserved.  
* Licensed under the MIT License.  See License in the project root 
* for license information.
* 
* DynamicsRequest File
* PHP version 7
*
* @category  Library
* @package   Microsoft.Dynamics
* @copyright 2017 Saint Systems, LLC
* @license   https://opensource.org/licenses/MIT MIT License
* @version   GIT: 0.1.0
* @link      https://www.microsoft.com/en-us/dynamics365/
*/

namespace Microsoft\Dynamics\Http;

use GuzzleHttp\Client;
use Microsoft\Dynamics\Constants;
use Microsoft\Dynamics\Exception\DynamicsException;

/**
 * Class DynamicsRequest
 *
 * @category Library
 * @package  Microsoft.Dynamics
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://www.microsoft.com/en-us/dynamics365/
 */
class DynamicsRequest
{
    /**
    * A valid access token
    *
    * @var string
    */
    protected $accessToken;

    /**
    * The API version to use ("v1.0", "beta")
    *
    * @var string
    */
    protected $apiVersion;

    /**
    * The instance api url to call
    *
    * @var string
    */
    protected $instanceApiUrl;

    /**
    * The endpoint to call
    *
    * @var string
    */
    protected $endpoint;

    /**
    * The Guzzle client used to make the HTTP request
    *
    * @var Client
    */
    protected $guzzleClient;

    /**
    * An array of headers to send with the request
    *
    * @var array(string => string)
    */
    protected $headers;

    /**
    * The body of the request (optional)
    *
    * @var string
    */
    protected $requestBody;

    /**
    * The type of request to make ("GET", "POST", etc.)
    *
    * @var object
    */
    protected $requestType;

    /**
    * True if the response should be returned as
    * a stream
    *
    * @var bool
    */
    protected $returnsStream;

    /**
    * The return type to cast the response as
    *
    * @var object
    */
    protected $returnType;

    /**
    * The timeout, in seconds
    *
    * @var string
    */
    protected $timeout;

    /**
    * Constructs a new Dynamics Request object
    *
    * @param string $requestType    The HTTP method to use, e.g. "GET" or "POST"
    * @param string $endpoint       The Dynamics endpoint to call
    * @param string $accessToken    A valid access token to validate the Dynamics call
    * @param string $instanceApiUrl The instance URL to call
    * @param string $apiVersion     The API version to use
     *
     * @throws DynamicsException when no access token is provided
    */ 
    public function __construct($requestType, $endpoint, $accessToken, $instanceApiUrl, $apiVersion)
    {
        $this->requestType = $requestType;
        $this->endpoint = $endpoint;
        $this->accessToken = $accessToken;
        $this->instanceApiUrl = $instanceApiUrl;

        if (!$this->accessToken) {
            throw new DynamicsException(Constants::NO_ACCESS_TOKEN);
        }

        if (empty($this->instanceApiUrl)) {
            throw new DynamicsException(Constants::INSTANCE_API_URL_MISSING);
        }

        $this->apiVersion = $apiVersion;
        $this->timeout = 0;
        $this->headers = $this->_getDefaultHeaders();
    }

    /**
    * Sets the return type of the response object
    *
    * @param mixed $returnClass The object class to use
    *
    * @return DynamicsRequest object
    */
    public function setReturnType($returnClass)
    {
        $this->returnType = $returnClass;
        if (strcasecmp($this->returnType, 'stream') == 0) {
            $this->returnsStream  = true;
        } else {
            $this->returnsStream = false;
        }
        return $this;
    }

    /**
    * Adds custom headers to the request
    *
    * @param array $headers An array of custom headers
    *
    * @return DynamicsRequest object
    */
    public function addHeaders($headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
    * Get the request headers
    *
    * @return array of headers
    */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
    * Attach a body to the request. Will JSON encode 
    * any Microsoft\Dynamics\Model objects as well as arrays
    *
    * @param mixed $obj The object to include in the request
    *
    * @return DynamicsRequest object
    */
    public function attachBody($obj)
    {
        // Attach streams & JSON automatically
        if (is_string($obj) || is_a($obj, 'GuzzleHttp\\Psr7\\Stream')) {
            $this->requestBody = $obj;
        } 
        // JSON-encode the model object's property dictionary
        else if (method_exists($obj, 'getProperties')) {
            $class = get_class($obj);
            $class = explode("\\", $class);
            $model = strtolower(end($class));
            
            $body = $this->flattenDictionary($obj->getProperties());
            $this->requestBody = "{" . $model . ":" . json_encode($body) . "}";
        } 
        // By default, JSON-encode (i.e. arrays)
        else {
            $this->requestBody = json_encode($obj);
        }
        return $this;
    }

    /**
    * Get the body of the request
    *
    * @return mixed request body of any type
    */
    public function getBody()
    {
        return $this->requestBody;
    }

    /**
    * Sets the timeout limit of the cURL request
    *
    * @param string $timeout The timeout in ms
    * 
    * @return DynamicsRequest object
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
     * @throws DynamicsException if response is invalid
     *
    * @return mixed object or array of objects
    *         of class $returnType
    */
    public function execute($client = null)
    {
        if (is_null($client)) {
            $client = $this->createGuzzleClient();
        }

        $result = $client->request(
            $this->requestType, 
            $this->getRequestUrl(), 
            [
                'body' => $this->requestBody,
                'stream' =>  $this->returnsStream,
                'timeout' => $this->timeout
            ]
        );

        //Send back the bare response
        if ($this->returnsStream) {
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

        if ($this->returnType) {
            $returnObj = $response->getResponseAsObject($this->returnType);
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
    public function executeAsync($client = null)
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

    /**
    * Download a file from OneDrive to a given location
    *
    * @param string $path   The path to download the file to
    * @param mixed  $client The client to use in the request
    *
     * @throws DynamicsException if file path is invalid
     *
    * @return null
    */
    public function download($path, $client = null)
    {
        if (is_null($client)) {
            $client = $this->createGuzzleClient();
        }
        try {
            $file = fopen($path, 'w');

            $client->request(
                $this->requestType, 
                $this->getRequestUrl(), 
                [
                    'body' => $this->requestBody,
                    'sink' => $file
                ]
            );
            fclose($file);
        } catch(DynamicsException $e) {
            throw new DynamicsException(Constants::INVALID_FILE);
        }
        return null;
    }

    /**
    * Upload a file to OneDrive from a given location
    *
    * @param string $path   The path of the file to upload
    * @param mixed  $client The client to use in the request
    *
     * @throws DynamicsException if file is invalid
     *
    * @return mixed DriveItem or array of DriveItems
    */
    public function upload($path, $client = null)
    {
        if (is_null($client)) {
            $client = $this->createGuzzleClient();
        }
        try {
            $file = fopen($path, 'r');
            $stream = \GuzzleHttp\Psr7\stream_for($file);
            $this->requestBody = $stream;
            return $this->execute($client);
        } catch(DynamicsException $e) {
            throw new DynamicsException(Constants::INVALID_FILE);
        }
    }

    /**
    * Get a list of headers for the request
    *
    * @return array The headers for the request
    */
    private function _getDefaultHeaders()
    {
        $headers = [
            'Host' => $this->instanceApiUrl,
            'Content-Type' => 'application/json',
            Constants::MAX_ODATA_VERSION_HEADER => Constants::MAX_ODATA_VERSION,
            Constants::ODATA_VERSION_HEADER => Constants::ODATA_VERSION,
            Constants::PREFER_HEADER => Constants::ODATA_MAX_PAGE_SIZE_DEFAULT,
            'SdkVersion' => 'Dynamics-php-' . Constants::SDK_VERSION,
            'Authorization' => 'Bearer ' . $this->accessToken
        ];
        return $headers;
    }

    /**
    * Get the concatenated request URL
    *
    * @return string request URL
    */
    private function getRequestUrl()
    {
        return $this->apiVersion . $this->endpoint;
    }

    /**
    * Checks whether the endpoint currently contains query
    * parameters and returns the relevant concatenator for 
    * the new query string
    *
    * @return string "?" or "&"
    */
    protected function getConcatenator()
    {
        if (stripos($this->endpoint, "?") === false) {
            return "?";
        }
        return "&";
    }

    /**
    * Create a new Guzzle client
    * To allow for user flexibility, the 
    * client is not reused. This allows the user
    * to set and change headers on a per-request
    * basis
    *
    * @return \GuzzleHttp\Client The new client
    */
    protected function createGuzzleClient()
    {
        $client = new Client(
            [
                'base_uri' => $this->instanceApiUrl,
                'headers' => $this->headers
            ]
        );
        return $client;
    }

    /**
    * Flattens the property dictionaries into 
    * JSON-friendly arrays
    *
    * @param mixed $obj the object to flatten
    *
    * @return array flattened object
    */
    protected function flattenDictionary($obj) {
        foreach ($obj as $arrayKey => $arrayValue) {
            if (method_exists($arrayValue, 'getProperties')) {
                $data = $arrayValue->getProperties();
                $obj[$arrayKey] = $data;
            } else {
                $data = $arrayValue;
            }
            if (is_array($data)) {
                $newItem = $this->flattenDictionary($data);
                $obj[$arrayKey] = $newItem;
            }
        }
        return $obj;
    }
}
