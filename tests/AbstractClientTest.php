<?php
namespace Da\Google\Places\Test;

use Da\Google\Places\Client\AbstractClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class AbstractClientTest extends \PHPUnit_Framework_TestCase
{
    public function testGetGuzzleClient()
    {
        $class = new \ReflectionClass(AbstractClient::class);

        $method = $class->getMethod('getGuzzleClient');
        $method->setAccessible(true);

        $instance = new AbstractClient('fakekey');

        $this->assertTrue($method->invoke($instance) instanceof Client);
    }

    public function testNullResponseOnRequest()
    {
        $mock = new MockHandler(
            [
                new Response(201, [], 'content'),
            ]
        );
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $instance = new TestAbstractClient('fakekey');
        $instance->setGuzzleClient($client);

        $class = new \ReflectionClass(AbstractClient::class);

        $method = $class->getMethod('request');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($instance, 'fake', 'get'));
    }
}

class TestAbstractClient extends AbstractClient
{
    public function setGuzzleClient($client)
    {
        $this->guzzle = $client;
    }
}
