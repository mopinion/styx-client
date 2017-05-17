<?php

namespace Mopinion\Api;

use Mopinion\Api\BaseClient;
use Mopinion\Api\Response\Response;

/**
 * Extension for the Base Client class
 * This class moslty contains convenience methods to reach the API endpoints
 */
class Client extends BaseClient
{

    public function __construct($domain=null, $publicKey=null, $privateKey=null)
    {
        parent::__construct($domain, $publicKey, $privateKey);
    }


    /**
     * Check if the API is accessible
     * @return Mopinion\Api\Response\PingResponse
     */
    public function ping()
    {
        $this->_setRoute( self::ROUTE_ROOT );

        return $this->_makeRequest( Response::MODEL_PING );
    }


    /**
     * Retrieve the signature token
     * @return Mopinion\Api\Response\TokenResponse
     */
    public function getSignatureToken($publicKey=null, $privateKey=null)
    {
        if( !empty($publicKey) ) { $this->setPublicKey( $publicKey ); }
        if( !empty($privateKey) ) { $this->setPrivateKey( $privateKey ); }

        $this->_setRoute( self::ROUTE_TOKEN );

        return $this->_makeRequest( Response::MODEL_TOKEN );
    }


    /**
     * Retrieve Account data
     * @return Mopinion\Api\Response\Response
     */
    public function getAccount()
    {
        $this->_setRoute( self::ROUTE_ACCOUNT );

        return $this->_makeRequest();
    }

}