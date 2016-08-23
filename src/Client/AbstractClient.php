<?php
/**
 * @copyright Copyright (c) 2013-2016 2amigOS! Consulting Group LLC
 *
 * @link http://2amigos.us
 *
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace Da\Google\Places\Client;

use GuzzleHttp\Client as HttpClient;
use InvalidArgumentException;
use SimpleXMLElement;

/**
 * Class Client is the base class of all objects in library. Handles common requests and Guzzle PHP client initialization.
 *
 * @author Antonio Ramirez <hola@2amigos.us>
 *
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 */
class AbstractClient
{
    /**
     * @var string response format. Can be json or xml.
     */
    protected $format = 'json';
    /**
     * @var string API endpoint
     */
    protected $api = 'https://maps.googleapis.com/maps/api/place/{cmd}/{format}';
    /**
     * @var string your API key
     */
    protected $key;
    /**
     * @var \GuzzleHttp\Client a client to make requests to the API
     */
    protected $guzzle;

    /**
     * AbstractClient constructor.
     *
     * @param $key
     * @param string $format
     *
     * @throws InvalidArgumentException
     */
    public function __construct($key, $format = 'json')
    {
        if (empty($key) || empty($format)) {
            throw new InvalidArgumentException('"key" and/or "format" cannot be empty.');
        }

        $this->key = $key;
        $this->format = $format;
    }

    /**
     * Required for child classes.
     *
     * @param string $cmd the command
     *
     * @return string
     */
    public function getUrl($cmd)
    {
        return strtr($this->api, ['{cmd}' => $cmd, '{format}' => $this->format]);
    }

    /**
     * Makes a Url request and returns its response.
     *
     * @param string $cmd the command
     * @param string $method the method 'get' or 'post'
     * @param array $params the parameters to be bound to the call
     * @param array $options the options to be attached to the client
     *
     * @return mixed|null
     */
    protected function request($cmd, $method = 'get', $params = [], $options = [])
    {
        $params = array_merge($params, ['key' => $this->key]);

        $response = $this->getGuzzleClient()->request(
            $method,
            $this->getUrl($cmd),
            array_merge(['query' => $params], $options)
        );

        if ($response->getStatusCode() === 200) {
            return $this->parseResponse($response->getBody()->getContents());
        } else {
            return null;
        }
    }

    /**
     * Parses response body in json or xml format as specified.
     *
     * @param string $contents
     *
     * @return mixed|SimpleXMLElement
     */
    protected function parseResponse($contents)
    {
        return $this->format == 'xml' ? new SimpleXMLElement($contents) : json_decode($contents);
    }

    /**
     * Returns an option from an array. If not set return default value.
     *
     * @param array $options
     * @param string $param
     * @param mixed $default
     *
     * @return mixed|null
     */
    protected function getParamValue($options, $param, $default = null)
    {
        return isset($options[$param]) ? $options[$param] : $default;
    }

    /**
     * Returns the guzzle client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getGuzzleClient()
    {
        if ($this->guzzle == null) {
            $this->guzzle = new HttpClient();
        }

        return $this->guzzle;
    }
}
