<?php
use PHPUnit\Framework\TestCase;
use Microsoft\Dynamics\Dynamics;
use Microsoft\Dynamics\Http\DynamicsRequest;
use Microsoft\Dynamics\Exception\DynamicsException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;

class HttpTest extends TestCase
{
    public $client;
    public $getRequest;
    public $container;

    public function setUp()
    {
        $mock = new MockHandler([
            new Response(200, ['foo' => 'bar']),
            new Response(200, ['foo' => 'bar'])
        ]);
        $this->container = [];
        $history = GuzzleHttp\Middleware::history($this->container);
        $handler = HandlerStack::create($mock);
        $handler->push($history);
        $this->client = new Client(['handler' => $handler]);

        $this->getRequest = new DynamicsRequest("GET", "/endpoint", "token", "instanceUrl", "version");
    }

    public function testGet()
    {
        $response = $this->getRequest->execute($this->client);
        $code = $response->getStatus();

        $this->assertEquals("200", $code);
    }

    public function testPost()
    {
        $request = new DynamicsRequest("POST", "/endpoint", "token", "instanceUrl", "version");
        $response = $request->execute($this->client);
        $code = $response->getStatus();

        $this->assertEquals("200", $code);
    }

    public function testPut()
    {
        $request = new DynamicsRequest("PUT", "/endpoint", "token", "instanceUrl", "version");
        $response = $request->execute($this->client);
        $code = $response->getStatus();

        $this->assertEquals("200", $code);
    }

    public function testPatch()
    {
        $request = new DynamicsRequest("PATCH", "/endpoint", "token", "instanceUrl", "version");
        $response = $request->execute($this->client);
        $code = $response->getStatus();

        $this->assertEquals("200", $code);
    }

    public function testUpdate()
    {
        $request = new DynamicsRequest("UPDATE", "/endpoint", "token", "instanceUrl", "version");
        $response = $request->execute($this->client);
        $code = $response->getStatus();

        $this->assertEquals("200", $code);
    }

    public function testDelete()
    {
        $request = new DynamicsRequest("DELETE", "/endpoint", "token", "instanceUrl", "version");
        $response = $request->execute($this->client);
        $code = $response->getStatus();

        $this->assertEquals("200", $code);
    }

    public function testInvalidVerb()
    {
        $this->expectException(GuzzleHttp\Exception\ClientException::class);

        $mock = new MockHandler([
            new Response(400, ['foo' => 'bar'])
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $request = new DynamicsRequest("OBLITERATE", "/endpoint", "token", "instanceUrl", "version");
        $response = $request->execute($client);
        $code = $response->getStatus();

        $this->assertEquals("400", $code);
    }

    public function testSendJson()
    {
        $body = json_encode(array('1' => 'a', '2' => 'b'));

        $request = $this->getRequest->attachBody($body);
        $this->assertInstanceOf(DynamicsRequest::class, $request);

        $response = $request->execute($this->client);
        $this->assertInstanceOf(Microsoft\Dynamics\Http\DynamicsResponse::class, $response);
        $this->assertEquals($body, $this->container[0]['request']->getBody()->getContents());
    }

    public function testSendArray()
    {
        $body = array('1' => 'a', '2' => 'b');
        $request = $this->getRequest->attachBody($body);
        $this->assertInstanceOf(DynamicsRequest::class, $request);

        $response = $request->execute($this->client);
        $this->assertInstanceOf(Microsoft\Dynamics\Http\DynamicsResponse::class, $response);
        $this->assertEquals(json_encode($body), $this->container[0]['request']->getBody()->getContents());
    }

    public function testSendObject()
    {
        $lead = new Microsoft\Dynamics\Model\Lead();
        $lead->firstname = 'Bob';
        $request = $this->getRequest->attachBody($lead);
        $this->assertInstanceOf(DynamicsRequest::class, $request);

        $response = $request->execute($this->client);
        $this->assertInstanceOf(Microsoft\Dynamics\Http\DynamicsResponse::class, $response);
        $this->assertEquals("{lead:".json_encode($lead->getProperties())."}", $this->container[0]['request']->getBody()->getContents());
    }

    public function testSendString()
    {
        $body = '{"1":"a","2":"b"}';
        $request = $this->getRequest->attachBody($body);
        $this->assertInstanceOf(DynamicsRequest::class, $request);

        $response = $request->execute($this->client);
        $this->assertInstanceOf(Microsoft\Dynamics\Http\DynamicsResponse::class, $response);
        $this->assertEquals($body, $this->container[0]['request']->getBody()->getContents());
    }

    public function testSendStream()
    {
        $body = GuzzleHttp\Psr7\stream_for('stream');
        $request = $this->getRequest->attachBody($body);
        $this->assertInstanceOf(DynamicsRequest::class, $request);

        $response = $request->execute($this->client);
        $this->assertInstanceOf(Microsoft\Dynamics\Http\DynamicsResponse::class, $response);
        $this->assertEquals($body, $this->container[0]['request']->getBody()->getContents());
    }
}