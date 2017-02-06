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
    private $_accessToken;
    /**
    * The api version to use ("v8.0", "v8.1", "v8.2")
    * Default is "v8.2"
    *
    * @var string
    */
    private $_apiVersion;
    /**
    * The Dynamics instance url to call
    *
    * @var string
    */
    private $_instanceUrl;

    /**
    * Creates a new Dynamics object, which is used to call the Dynamics 365 API
    */
    public function __construct($instanceUrl = null, $accessToken = null, $apiVersion = null)
    {
        $this->_instanceUrl = $instanceUrl;
        $this->_accessToken = $accessToken;
        $this->_apiVersion = $apiVersion;

        if (empty($this->_apiVersion)) {
            $this->_apiVersion = DynamicsConstants::API_VERSION;
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
        $this->_instanceUrl = $instanceUrl;
        return $this;
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
        $this->_apiVersion = $apiVersion;
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
        $this->_accessToken = $accessToken;
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
            $this->_accessToken, 
            $this->_instanceUrl, 
            $this->_apiVersion
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
            $this->_accessToken, 
            $this->_instanceUrl, 
            $this->_apiVersion
        );
    }
}
