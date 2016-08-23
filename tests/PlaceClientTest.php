<?php
namespace Da\Google\Places\Test;

use Da\Google\Places\Client\PlaceClient;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class PlaceClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestPlaceClient
     */
    private $client;

    protected function setUp()
    {
        parent::setUp();

        $this->client = new TestPlaceClient('fakegoogleapikey');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentExceptionOnConstruct()
    {
        $place = new PlaceClient('');
    }

    public function testPlaceDetailsRequest()
    {
        $this->mockClientGuzzleAndResponse(file_get_contents(__DIR__ . '/data/place-details-response.json'));
        $response = $this->client->details('fakeplaceid');

        $this->assertTrue($response->status === 'OK');
        $this->assertEquals('48 Pirrama Road, Pyrmont NSW, Australia', $response->result->formatted_address);
    }

    public function testPlaceAddRequest()
    {
        $this->mockClientGuzzleAndResponse(file_get_contents(__DIR__ . '/data/place-add-response.json'));
        $response = $this->client->add(
            ['lat' => -33.8669710, 'lng' => 151.1958750],
            'Google Shoes!',
            ['shoe_store'],
            50,
            'en-AU',
            [
                'website' => 'http://www.google.com.au',
                'phone_number' => '(02) 9374 4000',
            ]
        );

        $this->assertEquals('CdIJN2t_tDeuEmsRUsoyG83frY4', $response->place_id);

        $this->expectException('\InvalidArgumentException');
        $response = $this->client->add(
            ['lat' => -33.8669710, 'lng' => 151.1958750],
            'LoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsum' .
            'LoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsum' .
            'LoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsum' .
            'LoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsumLoremIpsum',
            ['shoe_store'],
            50,
            'en-AU',
            [
                'website' => 'http://www.google.com.au',
                'phone_number' => '(02) 9374 4000',
            ]
        );
    }

    public function testDeleteRequest()
    {
        $this->mockClientGuzzleAndResponse(file_get_contents(__DIR__ . '/data/place-delete-response.json'));

        $response = $this
            ->client
            ->delete(
                'CiQgAAAAeTQS1RtzAyVRVjHcRiIWmWeqcAl3k7bluW7GINLDULESEHozTQhy6OHJw03ziDvY1uEaFAP_vDRhK-UbWw3Gd7Ulqm3eRjIs'
            );

        $this->assertTrue($response->status === 'OK');
    }

    public function testPhotoRequest()
    {
        $img = file_get_contents(__DIR__ . '/data/2amigos.png');
        $this->mockClientGuzzleAndResponse($img);

        $response = $this->client->photo('fakephotoreference', ['maxwidth' => 400, 'maxheight' => 400]);
        $this->assertEquals($response, $img);

        $this->expectException('\InvalidArgumentException');
        $response = $this->client->photo('fakephotoreference');
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

class TestPlaceClient extends PlaceClient
{
    public function setGuzzleClient($client)
    {
        $this->guzzle = $client;
    }
}
