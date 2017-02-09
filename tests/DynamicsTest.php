<?php

use Microsoft\Dynamics\Tests\DynamicsTestCase;
use Microsoft\Dynamics\Constants;
use Microsoft\Dynamics\Dynamics;
use Microsoft\Dynamics\Http\DynamicsRequest;

class DynamicsTest extends DynamicsTestCase
{
    const TEST_INSTANCE_URL = 'https://contoso.crm.dynamics.com';
    const TEST_INSTANCE_2_URL = 'https://contoso.crm2.dynamics.com';

    public function testDynamicsConstructor()
    {
        $dynamics = new Dynamics();
        $this->assertNotNull($dynamics);
    }

    public function testDynamicsConstructorWithInstanceUrl()
    {
        $dynamics = new Dynamics(self::TEST_INSTANCE_URL);
        $instanceUrl = $this->readAttribute($dynamics, 'instanceUrl');
        $instanceApiUrl = $this->readAttribute($dynamics, 'instanceApiUrl');
        $this->assertEquals(self::TEST_INSTANCE_URL, $instanceUrl);
        $this->assertEquals(self::TEST_INSTANCE_URL . '/api/data/', $instanceApiUrl);
    }

    public function testDynamicsConstructorWithInstanceUrlAndAccessToken()
    {
        $dynamics = new Dynamics(self::TEST_INSTANCE_URL, 'abc');
        $accessToken = $this->readAttribute($dynamics, 'accessToken');
        $this->assertEquals('abc', $accessToken);
    }

    public function testDynamicsInstanceUrlSetter()
    {
        $dynamics = new Dynamics();
        $dynamics->setInstanceUrl(self::TEST_INSTANCE_URL);
        $instanceUrl = $this->readAttribute($dynamics, 'instanceUrl');
        $instanceApiUrl = $this->readAttribute($dynamics, 'instanceApiUrl');
        $this->assertEquals(self::TEST_INSTANCE_URL, $instanceUrl);
        $this->assertEquals(self::TEST_INSTANCE_URL . '/api/data/', $instanceApiUrl);
    }

    public function testDynamicsAccessTokenSetter()
    {
        $dynamics = new Dynamics();
        $dynamics->setAccessToken('abc');
        $accessToken = $this->readAttribute($dynamics, 'accessToken');
        $this->assertEquals('abc', $accessToken);
    }

    public function testDynamicsDefaultApiVersion()
    {
        $dynamics = new Dynamics();
        $apiVersion = $this->readAttribute($dynamics, 'apiVersion');
        $this->assertEquals(Constants::API_VERSION, $apiVersion);
    }

    public function testDynamicsApiVersionSetter()
    {
        $dynamics = new Dynamics();
        $dynamics->setApiVersion('v8.0');
        $apiVersion = $this->readAttribute($dynamics, 'apiVersion');
        $this->assertEquals('v8.0', $apiVersion);
    }

    public function testInitializeEmptyDynamics()
    {
        $this->expectException(Microsoft\Dynamics\Exception\DynamicsException::class);
        $dynamics = new Dynamics();
        $request = $dynamics->createRequest('GET', '/me');
    }

    public function testInitializeDynamicsWithTokenButNoInstanceUrl()
    {
        $this->expectException(Microsoft\Dynamics\Exception\DynamicsException::class);
        $dynamics = new Dynamics();
        $dynamics->setAccessToken('abc');
        $request = $dynamics->createRequest('GET', '/me');
    }

    public function testCreateRequestWithTokenAndNoInstanceUrl()
    {
        $this->expectException(Microsoft\Dynamics\Exception\DynamicsException::class);
        $dynamics = new Dynamics();
        $dynamics->setAccessToken('abc');
        $request = $dynamics->createRequest('GET', '/me');
    }

    public function testCreateRequestWithTokenAndInstanceUrl()
    {
        $dynamics = new Dynamics();
        $dynamics->setInstanceUrl(self::TEST_INSTANCE_URL)
                 ->setAccessToken('abc');
        $request = $dynamics->createRequest('GET', '/me');

        $this->assertInstanceOf(DynamicsRequest::class, $request);
    }

    public function testCreateRequestWithTokenAndInstanceUrlViaConstructor()
    {
        $dynamics = new Dynamics(self::TEST_INSTANCE_URL, 'abc');
        $request = $dynamics->createRequest('GET', '/me');

        $this->assertInstanceOf(DynamicsRequest::class, $request);
    }

    public function testCreateCollectionRequest()
    {
        $dynamics = new Dynamics();
        $dynamics->setInstanceUrl(self::TEST_INSTANCE_URL)
                 ->setAccessToken('abc');
        $request = $dynamics->createCollectionRequest('GET', '/me');

        $this->assertInstanceOf(DynamicsRequest::class, $request);
    }

    public function testRequestInstanceUrl()
    {
        $dynamics = new Dynamics();
        $dynamics->setInstanceUrl(self::TEST_INSTANCE_URL)
                 ->setAccessToken('abc');

        $request = $dynamics->createRequest('GET', '/me');
        $requestUrl = $this->readAttribute($request, 'instanceApiUrl');
        $this->assertEquals(self::TEST_INSTANCE_URL . '/api/data/', $requestUrl);
    }

    public function testRequestVersion()
    {
        $dynamics = new Dynamics();
        $dynamics->setInstanceUrl(self::TEST_INSTANCE_URL)
                 ->setAccessToken('abc')
                 ->setApiVersion('v8.0');
        $request = $dynamics->createRequest('GET', '/me');

        $this->assertEquals('v8.0', $this->readAttribute($request, 'apiVersion'));
    }

    public function testMultipleDynamicsObjects()
    {
        $dynamics = new Dynamics();
        $dynamics2 = new Dynamics();

        $dynamics->setInstanceUrl(self::TEST_INSTANCE_URL)
                 ->setAccessToken('abc');

        $dynamics2->setInstanceUrl(self::TEST_INSTANCE_2_URL)
                  ->setAccessToken('abc')
                  ->setApiVersion('v8.0');

        $request = $dynamics->createRequest('GET', '/me');
        $request2 = $dynamics2->createRequest('GET', '/me');

        $this->assertEquals(self::TEST_INSTANCE_URL . '/api/data/', $this->readAttribute($request, 'instanceApiUrl'));
        $this->assertEquals(self::TEST_INSTANCE_2_URL . '/api/data/', $this->readAttribute($request2, 'instanceApiUrl'));
        $this->assertEquals(Constants::API_VERSION, $this->readAttribute($request, 'apiVersion'));
        $this->assertEquals('v8.0', $this->readAttribute($request2, 'apiVersion'));
        
    }

}
