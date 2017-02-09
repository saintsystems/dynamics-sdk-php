<?php

namespace Microsoft\Dynamics\Http;

use Closure;
use Microsoft\Core\Http\HttpProvider;
use Microsoft\Core\Http\IHttpProvider;
use Microsoft\Dynamics\Exception\DynamicsException;

/**
 * A default IBaseClient implementation.
 */
class BaseClient implements IBaseClient
{
    /**
     * The base service URL. For example, "https://contoso.crm.dynamics.com/api/data/v8.0."
     * @var string
     */
    private $baseUrl;

    /**
     * The IAuthenticationProvider for authenticating request messages.
     * @var IAuthenticationProvider
     */
    private $authenticationProvider;

    /**
     * The IHttpProvider for sending HTTP requests.
     * @var IHttpProvider
     */
    private $httpProvider;
    
    /**
     * Constructs a new BaseClient.
     * @param string                  $baseUrl                The base service URL. For example, "https://contoso.crm.dynamics.com/api/data/v8.0."
     * @param IAuthenticationProvider $authenticationProvider The IAuthenticationProvider for authenticating request messages.
     * @param IHttpProvider|null      $httpProvider           The IHttpProvider for sending requests.
     */
    public function __construct(string $baseUrl, 
                                Closure $authenticationProvider, 
                                IHttpProvider $httpProvider = null)
    {
        $this->setBaseUrl($baseUrl);
        $this->authenticationProvider = $authenticationProvider;
        $this->httpProvider = $httpProvider ?? new HttpProvider();//new HttpProvider(new Serializer());
    }

    /**
     * Gets the IAuthenticationProvider for authenticating requests.
     * @var IAuthenticationProvider
     */
    

    public function getAuthenticationProvider()
    {
        return $this->authenticationProvider;
    }

    /**
     * Gets the base URL for requests of the client.
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Sets the base URL for requests of the client.
     * @param void
     */
    public function setBaseUrl($value)
    {
        if (empty($value))
        {
            throw new DynamicsException('Base URL is Missing');
                // new Error
                // {
                //     Code = ErrorConstants.Codes.InvalidRequest,
                //     Message = ErrorConstants.Messages.BaseUrlMissing,
                // });
        }

        $this->baseUrl = rtrim($value, '/');
    }

    /**
     * Gets the IHttpProvider for sending HTTP requests.
     * @var IHttpProvider
     */
    public function getHttpProvider()
    {
        return $this->httpProvider;
    }

}
