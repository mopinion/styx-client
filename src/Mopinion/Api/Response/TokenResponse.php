<?php

namespace Mopinion\Api\Response;

use Mopinion\Api\Response\Response;
use GuzzleHttp\Psr7\Response as GuzzleResponse;


/**
 * Response model for /token endpoint
 */
class TokenResponse extends Response
{
    public function __construct(GuzzleResponse $response)
    {
        parent::__construct($response);
    }


    /**
     * Overload default method to return only token, instead of json
     * @return string signature token string
     */
    public function __toString()
    {
        return json_decode($this->bodyString,false)->token;
    }

}