<?php

namespace Microsoft\Dynamics\Http;

use Microsoft\Core\Http\HttpRequestMessage;

/**
 * Interface for authenticating requests.
 */
interface IAuthenticationProvider
{
    /**
     * Authenticates the specified request message.
     * @param  HttpRequestMessage $request The HttpRequestMessage to authenticate.
     * @return void
     */
    public function authenticateRequest(HttpRequestMessage $request);
}