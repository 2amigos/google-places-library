<?php
namespace Da\Google\Places\Test;

use Da\Google\Places\Client\SearchClient;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class SearchClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestSearchClient
     */
    private $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = new TestSearchClient('fakegoogleapikey');
    }

    public function testNearbySearch()
    {
        $this->mockClientGuzzleAndResponse(file_get_contents(__DIR__ . '/data/search-response.json'));
        $response = $this->client->nearby('-33.8670522,151.1957362', ['radius' => 500, 'rankby' => 'distance', 'name' => 'cruise']);

        $this->assertTrue($response->status === 'OK');

        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('Unrecognized rank \'fake\'');
        $this->client->nearby('-33.8670522,151.1957362', ['rankby' => 'fake']);
    }

    public function testNearbySearchInvalidRankby()
    {
        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage(
            'When using "rankby":"distance", you must specify at least one of the following: keyword, name, type.'
        );
        $this->client->nearby('-33.8670522,151.1957362', ['rankby' => 'distance', 'radius' => 500]);
    }

    public function testNearbySearchInvalidRadiusByProminence()
    {
        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('When using "rankby":"prominence" you must specify a radius.');
        $this->client->nearby('-33.8670522,151.1957362');
    }

    public function testTextSearch()
    {
        $this->mockClientGuzzleAndResponse(file_get_contents(__DIR__ . '/data/search-response.json'));
        $response = $this->client->text('restaurant');

        $this->assertTrue($response->status === 'OK');
    }

    public function testRadarSearch()
    {
        $this->mockClientGuzzleAndResponse(file_get_contents(__DIR__ . '/data/search-response.json'));
        $response = $this->client->radar('51.503186,-0.126446', 500, ['type' => 'museum']);

        $this->assertTrue($response->status === 'OK');

        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('When using radar you must include at least one of keyword, name, or type.');
        $this->client->radar('-33.8670522,151.1957362', 500);
    }

    public function testAutocompleteSearch()
    {
        $this->mockClientGuzzleAndResponse(file_get_contents(__DIR__ . '/data/search-autocomplete-response.json'));
        $response = $this->client->autoComplete('Vict', 'en', ['types' => 'museum']);

        $this->assertTrue($response->status === 'OK');
    }

    protected function mockClientGuzzleAndResponse($contents)
    {
        $mock = new MockHandler(
            [
                new Response(200, [], $contents),
            ]
        );
        $handler = HandlerStack::create($mock);
        $guzzle = new HttpClient(['handler' => $handler]);
        $this->client->setGuzzleClient($guzzle);
    }
}

class TestSearchClient extends SearchClient
{
    public function setGuzzleClient($client)
    {
        $this->guzzle = $client;
    }
}
