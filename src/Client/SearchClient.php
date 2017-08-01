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
 * Class Search handles places searching requests.
 *
 * @author Antonio Ramirez <hola@2amigos.us>
 *
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 *
 */
class SearchClient extends AbstractClient
{
    /**
     * Returns places within a specific area.
     *
     * @see https://developers.google.com/places/documentation/search
     * @see https://developers.google.com/places/documentation/supported_types
     * @see https://spreadsheets.google.com/pub?key=p9pdwsai2hDMsLkXsoM05KQ&gid=1
     *
     * @param string $location The latitude/longitude around which to retrieve Place information.
     *                         This must be specified as latitude,longitude.
     * @param array  $params   optional parameters
     *
     * @throws \InvalidArgumentException
     * @throws RequestException          if the request fails
     *
     * @return mixed|null
     */
    public function nearby($location, $params = [])
    {
        $rankBy = trim(strtolower($this->getParamValue($params, 'rankby', 'prominence')));
        if (!in_array($rankBy, ['distance', 'prominence'], true)) {
            throw new \InvalidArgumentException("Unrecognized rank '$rankBy'");
        }
        if ($rankBy == 'distance') {
            if (!isset($params['keyword']) && !isset($params['name']) && !isset($params['type'])) {
                throw new \InvalidArgumentException(
                    'When using "rankby":"distance", you must specify at least one of the following: keyword, name, type.'
                );
            }
            unset($params['radius']);
        }
        if ($rankBy == 'prominence' && !isset($params['radius'])) {
            throw new \InvalidArgumentException('When using "rankby":"prominence" you must specify a radius.');
        }

        $params['location'] = $location;
        $params['sensor'] = $this->getParamValue($params, 'sensor', 'false');

        return $this->request('nearbysearch', 'get', $params);
    }

    /**
     * Returns places based on a string.
     *
     * @see https://developers.google.com/places/documentation/search#TextSearchRequests
     *
     * @param string $query  The text string on which to search, for example: "restaurant". The Place service will return
     *                       candidate matches based on this string and order the results based on their perceived relevance.
     * @param array  $params optional parameters
     *
     * @throws RequestException if the request fails
     *
     * @return mixed|null
     */
    public function text($query, $params = [])
    {
        $params['query'] = $query;

        return $this->request('textsearch', 'get', $params);
    }

    /**
     * Returns places of a specific area.
     *
     * @param string $location The latitude/longitude around which to retrieve Place information. This must be specified
     *                         as latitude,longitude.
     * @param string $radius   Defines the distance (in meters) within which to return Place results. The maximum allowed
     *                         radius is 50 000 meters.
     * @param array  $params   optional parameters
     *
     * @throws \InvalidArgumentException
     * @throws RequestException          if the request fails
     *
     * @return mixed|null
     */
    public function radar($location, $radius, $params = [])
    {
        if (!isset($params['keyword']) && !isset($params['name']) && !isset($params['type'])) {
            throw new \InvalidArgumentException(
                'When using radar you must include at least one of keyword, name, or type.'
            );
        }
        $params['location'] = $location;
        $params['radius'] = $radius;

        return $this->request('radarsearch', 'get', $params);
    }

    /**
     * Returns place predictions based on specific text and optional geographic bounds.
     *
     * @see https://developers.google.com/places/documentation/autocomplete#place_autocomplete_requests
     * @see https://developers.google.com/places/documentation/autocomplete#example_autocomplete_requests
     *
     * @param string $input    The text string on which to search. The Place Autocomplete service will return candidate
     *                         matches based on this string and order results based on their perceived relevance.
     * @param string $language The language in which to return results.
     * @param array  $params   optional parameters
     *
     * @throws RequestException if the request fails
     *
     * @return mixed|null
     */
    public function autoComplete($input, $language = 'en', $params = [])
    {
        $params['input'] = $input;
        $params['language'] = $language;

        return $this->request('autocomplete', 'get', $params);
    }
}
