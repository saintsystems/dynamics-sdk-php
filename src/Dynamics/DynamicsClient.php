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

use Closure;
use Microsoft\Dynamics\Constants;
use Microsoft\Dynamics\Http\BaseClient;
use Microsoft\Dynamics\Http\IDynamicsClient;
use Microsoft\Dynamics\Http\DynamicsCollectionRequest;
use Microsoft\Dynamics\Http\DynamicsRequest;
use Microsoft\Dynamics\Http\EntityRequestBuilder;
use Microsoft\Dynamics\Exception\DynamicsException;

/**
 * Class Dynamics
 *
 * @category Library
 * @package  Microsoft.Dynamics
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://www.microsoft.com/en-us/dynamics365/
 */
class DynamicsClient extends BaseClient// implements IDynamicsClient
{
    /**
     * Constructs a new BaseClient.
     * @param string                  $baseUrl                The base service URL. For example, "https://contoso.crm.dynamics.com/api/data/v8.0."
     * @param IAuthenticationProvider $authenticationProvider The IAuthenticationProvider for authenticating request messages.
     * @param IHttpProvider|null      $httpProvider           The IHttpProvider for sending requests.
     */
    public function __construct($baseUrl, 
                                Closure $authenticationProvider = null, 
                                IHttpProvider $httpProvider = null)
    {
        parent::__construct($baseUrl, $authenticationProvider, $httpProvider);
    }

    /**
     * Handle dynamic method calls into entities.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return class_exists('Microsoft\Dynamics\Model\\'.$method);
        // $entityType = 'Microsoft\Dynamics\Model\\'.$method;
        // $entity = new $entityType();
        // $entity->setConnection($this);

        // return $entity;
    }

    /**
     * Dynamically retrieve properties on the entity.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if (class_exists('Microsoft\\Dynamics\\Model\\'.$key))
        {
            $entityName = 'Microsoft\\Dynamics\\Model\\'.$key;
            $entitySetName = $entityName::$entity;
            return new EntityRequestBuilder($this->getBaseUrl().'/'.$entitySetName, $this, $entityName);
        }
    }

}
