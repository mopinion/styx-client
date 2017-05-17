<?php

namespace Mopinion\Api\Response;

use Mopinion\Api\Response\Response;
use GuzzleHttp\Psr7\Response as GuzzleResponse;


/**
 * Response model for 'ping' method
 */
class PingResponse extends Response
{
    public function __construct(GuzzleResponse $response)
    {
        parent::__construct($response);
    }


    /**
     * Overload default method
     * @return string
     */
    public function __toString()
    {
        return 'Pong!';
    }

}