<?php 
/**
* Copyright (c) Saint Systems, LLC.  All Rights Reserved.  
* Licensed under the MIT License.  See License in the project root 
* for license information.
* 
* DynamicsCollectionRequest File
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

use Microsoft\Dynamics\Constants;
use Microsoft\Dynamics\Exception\DynamicsException;

/**
 * Class DynamicsCollectionRequest
 *
 * @category Library
 * @package  Microsoft.Dynamics
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://www.microsoft.com/en-us/dynamics365/
 */
class DynamicsCollectionRequest extends DynamicsRequest
{
    /**
    * The size of page to divide the collection into
    *
    * @var int
    */
    protected $pageSize;

    /**
    * The skip token to use in calling a new page of results
    *
    * @var string
    */
    protected $skipToken;

    /**
    * True if the user has reached the end of the collection
    *
    * @var bool
    */
    protected $end;

    /**
    * The endpoint that the user called (with query parameters)
    *
    * @var string
    */
    protected $originalEndpoint;
    
    /**
    * The return type that the user specified
    *
    * @var string
    */
    protected $originalReturnType;

    /**
    * Constructs a new DynamicsCollectionRequest object
    *
    * @param string $requestType The HTTP verb for the 
    *                            request ("GET", "POST", "PUT", etc.)
    * @param string $endpoint    The URI of the endpoint to hit
    * @param string $accessToken A valid access token
    * @param string $instanceUrl     The base URL of the request
    * @param string $apiVersion  The version of the API to call
    */
    public function __construct($requestType, $endpoint, $accessToken, $instanceUrl, $apiVersion)
    {
        parent::__construct(
            $requestType, 
            $endpoint, 
            $accessToken, 
            $instanceUrl, 
            $apiVersion
        );
        $this->end = false;
    }

    /**
    * Gets the number of entries in the collection
    *
    * @return int the number of entries
    */
    public function count()
    {
        $query = '$count=true';
        $request = new DynamicsRequest(
            $this->requestType, 
            $this->endpoint . $this->getConcatenator() . $query, 
            $this->accessToken, 
            $this->instanceUrl, 
            $this->apiVersion
        );
        $result = $request->execute()->getBody();

        if (array_key_exists("@odata.count", $result)) {
            return $result['@odata.count'];
        }

        /* The $count query parameter for the Dynamics API
           is available on several models but not all */
        trigger_error('Count unavailable for this collection');
        return null;
    }

    /**
    * Sets the number of results to return with each call
    * to "getPage()"
    *
    * @param int $pageSize The page size
    *
     * @throws DynamicsException if the requested page size exceeds
     *         the Dynamics's defined page size limit
    * @return DynamicsCollectionRequest object
    */
    public function setPageSize($pageSize)
    {
        if ($pageSize > Constants::MAX_PAGE_SIZE) {
            throw new DynamicsException(Constants::MAX_PAGE_SIZE_ERROR);
        }
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
    * Gets the next page of results
    *
    * @param bool $prev When true, get the previous page
    *
    * @return array of objects of class $returnType
    */
    public function getPage($prev = false)
    {
        $this->setPageCallInfo($prev);
        $response = $this->execute();

        return $this->processPageCallReturn($response);
    }

    /**
    * Sets the required query information to get a new page
    * 
    * @param bool $prev Set to true for the previous page
    *
    * @return DynamicsCollectionRequest
    */
    public function setPageCallInfo($prev) 
    {
        // Store these to add temporary query data to request
        $this->originalReturnType = $this->returnType;
        $this->originalEndpoint = $this->endpoint;

        /* This allows processPageCallReturn to receive
           all of the response data, not just the objects */
        $this->returnType = null;

        if ($this->end) {
            trigger_error('Reached end of collection');
            return null;
        }

        // Build the page navigation query string
        $query = '$top=' . $this->pageSize;
        if ($this->skipToken) {
            $query .='&$skiptoken=' . $this->skipToken;
        }
        if ($prev) {
            $query .='&previous-page=true';
        }

        $this->endpoint = $this->endpoint . $this->getConcatenator() . $query;
        return $this;
    }

    /**
    * Clean up after making a page call request
    *
    * @param DynamicsResponse $response The DynamicsResponse returned
    *        after making a page call
    *
    * @return mixed result of the call, formatted according
    *         to the returnType set by the user
    */
    public function processPageCallReturn($response)
    {
        $this->skipToken = $response->getSkipToken();

        /* If no skip token is returned, we have reached the end
           of the collection */
        if (!$this->skipToken) {
            $this->end = true;
        }

        $result = $response->getBody();

        // Cast as user-defined model
        if ($this->originalReturnType) {
            $result = $response->getResponseAsObject($this->originalReturnType);
        }

        // Restore user-defined parameters
        $this->endpoint = $this->originalEndpoint;
        $this->returnType = $this->originalReturnType;

        return $result;
    }

    /**
    * Gets the previous page of results from the collection
    *
    * @return array of objects of class $returnType
    */
    public function getPrevPage()
    {
        $this->end = false;
        return $this->getPage(true);
    }

    /**
    * Gets whether the user has reached the end of the collection
    *
    * @return bool The end
    */
    public function isEnd()
    {
        return $this->end;
    }
}
