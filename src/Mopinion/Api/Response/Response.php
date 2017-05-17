<?php

namespace Mopinion\Api\Response;

use GuzzleHttp\Psr7\Response as GuzzleResponse;


/**
 * Default response model
 */
class Response
{
    const MODEL_DEFAULT    = 'Mopinion\Api\Response\Response';
    const MODEL_PING       = 'Mopinion\Api\Response\PingResponse';
    const MODEL_TOKEN      = 'Mopinion\Api\Response\TokenResponse';

    public $reasonPhrase    = 'OK';
    public $statusCode      = 200;

    protected $bodyString   = null;

    private $guzzleResponse = null;
    private $headers        = [];


    public function __construct(GuzzleResponse $response)
    {
        $this->guzzleResponse = $response;

        $this->statusCode = $response->getStatusCode();
        $this->reasonPhrase = $response->getReasonPhrase();

        $this->headers = $response->getHeaders();

        $this->bodyString = (string) $response->getBody();
    }


    /**
     * Gets the value of statusCode.
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }


    /**
     * Gets the value of reasonPhrase.
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->reasonPhrase;
    }


    /**
     * Return the raw response from Guzzle
     * @return GuzzleHttp\Psr7\Response
    */
    public function getRaw()
    {
        return $this->guzzleResponse;
    }


    /**
     * Return the response body
     * @return mixed
     */
    public function getBody()
    {
        $decodedJSON = json_decode($this->bodyString);

        if($decodedJSON) {
            return $decodedJSON;
        }

        return $this->bodyString;
    }


    /**
     * Get the content of the specified header, if set
     * @return mixed
     */
    public function getHeader($headerName)
    {
        if( in_array($headerName, array_keys($this->headers)) ) {
            return $this->headers[$headerName];
        }

        return false;
    }


    /**
     * Get all response headers
     * @param  boolean $namesOnly if true, return only the header names
     * @return array
     */
    public function getHeaders($namesOnly=false)
    {
        if($namesOnly){
            return array_keys($this->headers);
        }

        return $this->headers;
    }


    /**
     * Return response body as a JSON encoded string
     * @return string
     */
    public function toJson()
    {
        // If the response data wasn't already JSON (which in most cases it should be)
        // create a simple JSON string for it now
        $isJson =  json_decode($this->bodyString);
        if(!$isJson){
            return json_encode( array('_response' => $this->bodyString ));
        }

        return $this->bodyString;
    }


    /**
     * Return response body as an array
     * @return array
     */
    public function toArray()
    {
        return json_decode($this->bodyString, true);
    }


    /**
     * Cast to string method
     * @return string
     */
    public function __toString()
    {
        return $this->bodyString;
    }

}