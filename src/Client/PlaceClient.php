<?php

/*
 * This file is part of the 2amigos/google-places-library project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Google\Places\Client;

use GuzzleHttp\Exception\RequestException;

/**
 * Class Place handles place details requests and common actions.
 *
 * @author Antonio Ramirez <hola@2amigos.us>
 *
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 */
class PlaceClient extends AbstractClient
{
    /**
     * Returns the details of a place.
     *
     * @see https://developers.google.com/places/documentation/details
     * @see https://spreadsheets.google.com/pub?key=p9pdwsai2hDMsLkXsoM05KQ&gid=1
     *
     * @param string $placeid  the place id
     * @param string $language the language to return the results. Defaults to 'en' (english).
     * @param array  $params   optional parameters
     *
     * @throws RequestException if the request fails
     *
     * @return mixed|null
     */
    public function details($placeid, $language = 'en', $params = [])
    {
        $params['placeid'] = $placeid;
        $params['language'] = $language;
        $params['sensor'] = $this->getParamValue($params, 'sensor', 'false');

        return $this->request('details', 'get', $params);
    }

    /**
     * Returns a photo content.
     *
     * @see https://developers.google.com/places/documentation/photos#place_photo_requests
     *
     * @param string $reference string identifier that uniquely identifies a photo. Photo references are returned from
     *                          either a [[Search::text]], [[Search::nearby]], [[Search::radar]] or [[Place::details]] request.
     * @param array  $params    optional parameters.
     *
     * @throws \InvalidArgumentException
     * @throws RequestException          if the request fails
     *
     * @return mixed|null
     */
    public function photo($reference, $params = [])
    {
        if (!isset($params['maxheight']) && !isset($params['maxwidth'])) {
            throw new \InvalidArgumentException('You must set "maxheight" or "maxwidth".');
        }
        $params['photoreference'] = $reference;
        $params['key'] = $this->key;
        $url = str_replace('/{format}', '', $this->api);
        $url = str_replace('{cmd}', 'photo', $url);
        $response = $this->getGuzzleClient()->get($url, ['query' => $params]);

        return $response->getBody();
    }

    /**
     * Adds a place on Google's places database for your application. This function only works with JSON formats, that
     * means that no matter what you set the [[$format]] to work with, it will be superseded by 'json' type.
     *
     * @see https://developers.google.com/places/documentation/actions#adding_a_place
     *
     * @param array  $location The textual latitude/longitude value from which you wish to add new place information.
     * @param string $name     The full text name of the place. Limited to 255 characters.
     * @param array  $types    The category in which this place belongs.
     * @param string $accuracy The accuracy of the location signal on which this request is based, expressed in meters.
     * @param string $language The language in which the place's name is being reported.
     * @param array  $params   The extra recommended but not required parameters (ie address, phone_number, and website)
     *
     * @throws \InvalidArgumentException
     * @throws RequestException          if the request fails
     *
     * @return array
     */
    public function add(array $location, $name, array $types, $accuracy, $language = 'en', array $params = [])
    {
        if (strlen($name) > 255) {
            throw new \InvalidArgumentException('"$name" cannot be larger than 255 chars');
        }
        $types = (array) $types;
        $data = $params;
        $data['location'] = $location;
        $data['name'] = $name;
        $data['types'] = $types;
        $data['accuracy'] = $accuracy;
        $data['language'] = $language;

        return $this->request(
            'add',
            'post',
            [
                'key' => $this->key,
            ],
            [
                'body' => json_encode($data),
            ]
        );
    }

    /**
     * Deletes a place. A place can only be deleted if:
     * - It was added by the same application as is requesting its deletion.
     * - It has not successfully passed through the Google Maps moderation process, and and is therefore not visible to
     * all applications.
     *
     * @param string $reference The textual identifier that uniquely identifies this place
     *
     * @throws RequestException if the request fails
     *
     * @return array
     */
    public function delete($reference)
    {
        return $this->request(
            'delete',
            'post',
            [
                'key' => $this->key,
            ],
            ['body' => json_encode(['reference' => $reference])]
        );
    }
}
