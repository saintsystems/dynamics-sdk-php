<?php 
/**
* Copyright (c) Saint Systems, LLC.  All Rights Reserved.  
* Licensed under the MIT License.  See License in the project root 
* for license information.
* 
* Dynamics File
* PHP version 7
*
* @category  Library
* @package   Microsoft.Dynamics
* @copyright 2017 Saint Systems, LLC
* @license   https://opensource.org/licenses/MIT MIT License
* @version   GIT: 0.1.0
* @link      https://www.microsoft.com/en-us/dynamics365/
*/

namespace Microsoft\Dynamics;

use Microsoft\Dynamics\Core\DynamicsConstants;
use Microsoft\Dynamics\Http\DynamicsCollectionRequest;
use Microsoft\Dynamics\Http\DynamicsRequest;
use Microsoft\Dynamics\Exception\DynamicsException;

/**
 * Class Dynamics
 *
 * @category Library
 * @package  Microsoft.Dynamics
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://www.microsoft.com/en-us/dynamics365/
 */
class Dynamics
{
    /**
    * The access_token provided after authenticating
    * with Azure AD for Online/365 instance or ADFS for On-Premises instances
    *
    * @var string
    */
    private $accessToken;

    /**
    * The api version to use ("v8.0", "v8.1", "v8.2")
    * Default is "v8.2"
    *
    * @var string
    */
    private $apiVersion;

    /**
    * The Dynamics instance url to call
    *
    * @var string
    */
    private $instanceUrl;

    /**
    * The Dynamics instance api url to call
    *
    * @var string
    */
    private $instanceApiUrl;

    /**
    * Creates a new Dynamics object, which is used to call the Dynamics 365 API
    */
    public function __construct($instanceUrl = null, $accessToken = null, $apiVersion = null)
    {
        if ( ! empty($instanceUrl)) {
            $this->parseInstanceUrl($instanceUrl);
        }
        $this->accessToken = $accessToken;
        $this->apiVersion = $apiVersion;

        if (empty($this->apiVersion)) {
            $this->apiVersion = DynamicsConstants::API_VERSION;
        }
    }

    /**
    * Sets the Dynamics Instance URL to call
    *
    * @param string $instanceUrl The URL to call
    *
    * @return Dynamics object
    */
    public function setInstanceUrl($instanceUrl)
    {
        $this->parseInstanceUrl($instanceUrl);
        return $this;
    }

    /**
     * Parse the instance url and reconstitute the instance API URL from it
     * @param  [type] $instanceUrl [description]
     * @return [type]              [description]
     */
    private function parseInstanceUrl($instanceUrl)
    {
        if (empty($instanceUrl)) {
            throw new DynamicsException(DynamicsConstants::INSTANCE_URL_MISSING);
        }

        $parsedUrl = parse_url($instanceUrl);

        $this->instanceUrl = $parsedUrl['scheme'].'://'.$parsedUrl['host'];

        // TODO: Discover Instance API URL and Version using Discovery Service
        $this->instanceApiUrl = str_replace('{scheme}',$parsedUrl['scheme'], DynamicsConstants::WEB_API_INSTANCE_ENDPOINT_FORMAT);
        $this->instanceApiUrl = str_replace('{instance_url}',$parsedUrl['host'], $this->instanceApiUrl);
    }

    /**
    * Sets the API version to use, e.g. "v8.0" (defaults to v8.2)
    *
    * @param string $apiVersion The API version to use
    *
    * @return Dynamics object
    */
    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
        return $this;
    }

    /**
    * Sets the access token. A valid access token is required
    * to run queries against Dynamics
    *
    * @param string $accessToken The user's access token, retrieved from AD or ADFS auth
    *
    * @return Dynamics object
    */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
    * Creates a new request object with the given Dynamics information
    *
    * @param string $requestType The HTTP method to use, e.g. "GET" or "POST"
    * @param string $endpoint    The Dynamics endpoint to call
    *
    * @return DynamicsRequest The request object, which can be used to 
    *                      make queries against Dynamics
    */
    public function createRequest($requestType, $endpoint)
    {
        return new DynamicsRequest(
            $requestType, 
            $endpoint, 
            $this->accessToken, 
            $this->instanceApiUrl, 
            $this->apiVersion
        );
    }

    /**
    * Creates a new collection request object with the given 
    * Dynamics information
    * 
    * @param string $requestType The HTTP method to use, e.g. "GET" or "POST"
    * @param string $endpoint    The Dynamics endpoint to call
    * 
    * @return DynamicsCollectionRequest The request object, which can be
    *                                used to make queries against Dynamics
    */
    public function createCollectionRequest($requestType, $endpoint)
    {
        return new DynamicsCollectionRequest(
            $requestType, 
            $endpoint, 
            $this->accessToken, 
            $this->instanceApiUrl, 
            $this->apiVersion
        );
    }
}
