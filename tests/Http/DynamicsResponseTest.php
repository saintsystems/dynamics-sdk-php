<?php
use PHPUnit\Framework\TestCase;
use Microsoft\Dynamics\Dynamics;
use Microsoft\Dynamics\Http\DynamicsRequest;
use Microsoft\Dynamics\Http\DynamicsResponse;
use Microsoft\Dynamics\Exception\DynamicsException;
use Microsoft\Dynamics\Model;

class DynamicsResponseTest extends TestCase
{
    public $client;
    public $request;
    public $responseBody;

    public function setUp()
    {
        $this->responseBody = array('body' => 'content', 'firstname' => 'Bob');
        $body = json_encode($this->responseBody);
        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(200, ['foo' => 'bar'], $body),
            new GuzzleHttp\Psr7\Response(200, ['foo' => 'bar'], $body)
        ]);
        $handler = GuzzleHttp\HandlerStack::create($mock);
        $this->client = new GuzzleHttp\Client(['handler' => $handler]);

        $this->request = new DynamicsRequest("GET", "/endpoint", "token", "instanceUrl", "version");
    }

    public function testGetResponseAsObject()
    {
        $this->request->setReturnType(Model\Lead::class);
        $response = $this->request->execute($this->client);

        $this->assertInstanceOf(Model\Lead::class, $response);
        $this->assertEquals($this->responseBody['firstname'], $response->getFirstName());

    }

    public function testGetSkipToken()
    {
        //Temporarily make getSkipToken() public
        $reflectionMethod = new ReflectionMethod('Microsoft\Dynamics\Http\DynamicsResponse', 'getSkipToken');
        $reflectionMethod->setAccessible(true);

        $body = json_encode(array('@odata.nextLink' => 'https://url.com/resource?$skiptoken=10'));
        $response = new DynamicsResponse($this->request, $body);

        $token = $reflectionMethod->invokeArgs($response, array());
        $this->assertEquals('10', $token);
    }

    public function testDecodeBody()
    {
        //Temporarily make decodeBody() public
        $reflectionMethod = new ReflectionMethod('Microsoft\Dynamics\Http\DynamicsResponse', '_decodeBody');
        $reflectionMethod->setAccessible(true);

        $response = new DynamicsResponse($this->request, json_encode($this->responseBody));
        $decodedBody = $reflectionMethod->invokeArgs($response, array());

        $this->assertEquals($this->responseBody, $decodedBody);
    }

    public function testDecodeEmptyBody()
    {
        //Temporarily make decodeBody() public
        $reflectionMethod = new ReflectionMethod('Microsoft\Dynamics\Http\DynamicsResponse', '_decodeBody');
        $reflectionMethod->setAccessible(true);

        $response = new DynamicsResponse($this->request);
        $decodedBody = $reflectionMethod->invokeArgs($response, array());

        $this->assertEquals(array(), $decodedBody);
    }

    public function testGetBody()
    {
        $response = $this->request->execute($this->client);
        $this->assertInstanceOf(DynamicsResponse::class, $response);

        $body = $response->getBody();
        $this->assertEquals($this->responseBody, $body);
    }

    public function testGetRawBody()
    {
        $response = $this->request->execute($this->client);

        $body = $response->getRawBody();
        $this->assertEquals(json_encode($this->responseBody), $body);
    }

    public function testGetStatus()
    {
        $response = $this->request->execute($this->client);

        $this->assertEquals('200', $response->getStatus());
    }
}
