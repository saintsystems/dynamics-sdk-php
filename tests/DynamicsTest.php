<?php

use Microsoft\Dynamics\Tests\DynamicsTestCase;
use Microsoft\Dynamics\Core\DynamicsConstants;
use Microsoft\Dynamics\Dynamics;
use Microsoft\Dynamics\Http\DynamicsRequest;

class DynamicsTest extends DynamicsTestCase
{
    public function testDynamicsConstructor()
    {
        $dynamics = new Dynamics();
        $this->assertNotNull($dynamics);
    }

    public function testInitializeEmptyDynamics()
    {
        $this->expectException(Microsoft\Dynamics\Exception\DynamicsException::class);
        $dynamics = new Dynamics();
        $request = $dynamics->createRequest("GET", "/me");
    }

    public function testInitializeDynamicsWithToken()
    {
        $dynamics = new Dynamics();
        $dynamics->setAccessToken('abc');
        $request = $dynamics->createRequest("GET", "/me");

        $this->assertInstanceOf(DynamicsRequest::class, $request);
    }

    public function testInitializeDynamicsWithInstanceUrl()
    {
        $instanceUrl = $this->resource;
        $dynamics = new Dynamics($instanceUrl);
        $dynamics->setAccessToken($this->accessToken);
        $request = $dynamics->createRequest("GET", "/me");

        $this->assertInstanceOf(DynamicsRequest::class, $request);
    }
}
