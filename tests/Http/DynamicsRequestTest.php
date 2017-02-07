<?php
use PHPUnit\Framework\TestCase;
use Microsoft\Dynamics\Core\DynamicsConstants;
use Microsoft\Dynamics\Dynamics;
use Microsoft\Dynamics\Http\DynamicsRequest;

class DynamicsRequestTest extends TestCase
{
    protected $requests;
    protected $defaultHeaders;
    protected $client;

    public function setUp()
    {
        $this->requests = array(
            new DynamicsRequest("GET", "/endpoint", "token", "instanceUrl", "version"),
            new DynamicsRequest("PATCH", "/endpoint?query", "token", "instanceUrl", "version"),
            new DynamicsRequest("GET", "/endpoint?query&query2", "token", "instanceUrl", "version")
        );

        $this->defaultHeaders = array(
            "Host" => "instanceUrl",
            "Content-Type" => "application/json",
            DynamicsConstants::MAX_ODATA_VERSION_HEADER => DynamicsConstants::MAX_ODATA_VERSION,
            DynamicsConstants::ODATA_VERSION_HEADER => DynamicsConstants::ODATA_VERSION,
            DynamicsConstants::PREFER_HEADER => DynamicsConstants::ODATA_MAX_PAGE_SIZE_DEFAULT,
            "SdkVersion" => "Dynamics-php-" . DynamicsConstants::SDK_VERSION,
            "SdkVersion" => "Dynamics-php-" . DynamicsConstants::SDK_VERSION,
            "Authorization" => "Bearer token"
        );

        $body = json_encode(array('body' => 'content'));
        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(200, ['foo' => 'bar'], $body),
            new GuzzleHttp\Psr7\Response(201, ['foo' => 'bar']),
            new GuzzleHttp\Psr7\Response(200, ['foo' => 'bar'], $body),
            new GuzzleHttp\Psr7\Response(201, ['foo' => 'bar'])
        ]);
        $handler = GuzzleHttp\HandlerStack::create($mock);
        $this->client = new GuzzleHttp\Client(['handler' => $handler]);
    }

    public function testSetReturnType()
    {
        //Temporarily make getRequestUrl() public
        $reflectionMethod = new ReflectionMethod('Microsoft\Dynamics\Http\DynamicsRequest', 'getRequestUrl');
        $reflectionMethod->setAccessible(true);

        $dynamics = new Dynamics();
        $dynamics->setInstanceUrl('https://contoso.crm.dynamics.com');
        $dynamics->setApiVersion('v8.0');
        $dynamics->setAccessToken('token');
        $request = $dynamics->createRequest('GET', '/leads');
        $dynamics->setApiVersion('v8.2');

        $requestUrl = $reflectionMethod->invokeArgs($request, array());
        $this->assertEquals($requestUrl, 'v8.0/leads');

        $request2 = $dynamics->createRequest('GET', '/leads');
        $requestUrl = $reflectionMethod->invokeArgs($request2, array());
        $this->assertEquals('v8.2/leads', $requestUrl);
    }

    public function testAddHeaders()
    {
        $testHeader = array("test" => "header");
        $request = $this->requests[0]->addHeaders($testHeader);
        $headers = $request->getHeaders();

        $expectedHeaders = array(
            "Host" => "instanceUrl",
            "Content-Type" => "application/json",
            DynamicsConstants::MAX_ODATA_VERSION_HEADER => DynamicsConstants::MAX_ODATA_VERSION,
            DynamicsConstants::ODATA_VERSION_HEADER => DynamicsConstants::ODATA_VERSION,
            DynamicsConstants::PREFER_HEADER => DynamicsConstants::ODATA_MAX_PAGE_SIZE_DEFAULT,
            "SdkVersion" => "Dynamics-php-" . DynamicsConstants::SDK_VERSION,
            "SdkVersion" => "Dynamics-php-" . DynamicsConstants::SDK_VERSION,
            "Authorization" => "Bearer token",
            "test" => "header"
        );

        $this->assertEquals($expectedHeaders, $headers);
    }

    public function testCustomHeadersOverwriteDefaults()
    {
        $testHeader = array("Content-Type" => "application/x-www-form-urlencoded");
        $request = $this->requests[0]->addHeaders($testHeader);
        $headers = $request->getHeaders();

        $expectedHeaders = array(
            "Host" => "instanceUrl",
            "Content-Type" => "application/x-www-form-urlencoded",
            DynamicsConstants::MAX_ODATA_VERSION_HEADER => DynamicsConstants::MAX_ODATA_VERSION,
            DynamicsConstants::ODATA_VERSION_HEADER => DynamicsConstants::ODATA_VERSION,
            DynamicsConstants::PREFER_HEADER => DynamicsConstants::ODATA_MAX_PAGE_SIZE_DEFAULT,
            "SdkVersion" => "Dynamics-php-" . DynamicsConstants::SDK_VERSION,
            "Authorization" => "Bearer token"
        );

        $this->assertEquals($expectedHeaders, $headers);
    }

    public function testDefaultHeaders()
    {
        $headers = $this->requests[0]->getHeaders();

        $this->assertEquals($this->defaultHeaders, $headers);
    }

    public function testExecute()
    {
        $response = $this->requests[0]->execute($this->client);

        $this->assertInstanceOf(Microsoft\Dynamics\Http\DynamicsResponse::class, $response);
    }

    public function testExecuteAsync()
    {
        $body = json_encode(array('body' => 'content'));

        $promise = $this->requests[0]
                         ->executeAsync($this->client);
        $this->assertInstanceOf(GuzzleHttp\Promise\PromiseInterface::class, $promise);

        $promise = $this->requests[1]
                         ->executeAsync($this->client);
        $this->assertInstanceOf(GuzzleHttp\Promise\PromiseInterface::class, $promise);

        $promise = $this->requests[0]
                         ->executeAsync($this->client);
        $promise2 = $this->requests[2]
                          ->executeAsync($this->client);

        $response = \GuzzleHttp\Promise\unwrap(array($promise));
        foreach ($response as $responseItem) {
            $this->assertInstanceOf(Microsoft\Dynamics\Http\DynamicsResponse::class, $responseItem);
        }
    }

    public function testGetRequestUrl()
    {
        //Temporarily make getRequestUrl() public
        $reflectionMethod = new ReflectionMethod('Microsoft\Dynamics\Http\DynamicsRequest', 'getRequestUrl');
        $reflectionMethod->setAccessible(true);

        $requestUrl = $reflectionMethod->invokeArgs($this->requests[0], array());
        $this->assertEquals($requestUrl, "version/endpoint");
    }

    public function testGetConcatenator()
    {
        //Temporarily make getConcatenator() public
        $reflectionMethod = new ReflectionMethod('Microsoft\Dynamics\Http\DynamicsRequest', 'getConcatenator');
        $reflectionMethod->setAccessible(true);

        $concatenator = $reflectionMethod->invokeArgs($this->requests[0], array());
        $this->assertEquals($concatenator, "?");

        $concatenator = $reflectionMethod->invokeArgs($this->requests[1], array());
        $this->assertEquals($concatenator, "&");

        $concatenator = $reflectionMethod->invokeArgs($this->requests[2], array());
        $this->assertEquals($concatenator, "&");
    }
}
