<?php

namespace Mopinion\Api;

use Mopinion\Api\Response\Response;
use Mopinion\Api\Route;
use GuzzleHttp\Client as HttpClient;


/**
 * Base class for the API client
 */
class BaseClient
{
    const VERSION       = '1.0.1';
    const API_URL       = 'https://api.mopinion.com';

    const HASH_METHOD   = 'sha256';
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';

    private static $privateKey         = null;
    private static $publicKey          = null;
    private static $domain             = null;
    private static $signatureToken     = null;
    private static $headers            = [];

    private $route              = null;
    private $data               = '';


    public function __construct($domain = null, $publicKey = null, $privateKey = null)
    {
        if( !empty( $domain ) ) { self::setDomain( $domain ); }
        if( !empty( $publicKey) ) { self::setPublicKey( $publicKey );  }
        if( !empty( $privateKey) ) { self::setPrivateKey( $privateKey );  }
    }


    /**
     * Make the request to the API
     * @param string $method method verb
     * @return response object
     */
    protected function _makeRequest($responseModel = Response::MODEL_DEFAULT, $method = self::METHOD_GET )
    {
        // Create our http client using guzzle
        $client = new HttpClient(['base_uri' => self::API_URL, 'http_errors' => false ] );

        $requestOptions = [];

        // The route for getting the token requires basic authentication, rather than a signature
        if( $this->route === Route::TOKEN ) {
            $requestOptions['auth'] = array(self::$publicKey, self::$privateKey);

        }else if ($this->route !== Route::ROOT ){

            self::_addHeader('X-Auth-Token', $this->_generateAuthToken() );
        }

        // Add the request headers
        $requestOptions['headers'] = $this->_getHeaders();

        $response = $client->request($method, $this->route, $requestOptions);

        return new $responseModel($response);
    }


    /**
     * Generate a HMAC signature for the current route and data
     * @return string
     */
    protected function _generateSignature()
    {
        if(empty(self::$signatureToken)){
            $code = 1;
            throw new \Exception("Signature token is not set, error code:{$code}");
        }

        // overload signature token for demo settings
        if (self::$privateKey === 'mpn_demo_private'){ return 'mpn_demo_signature'; }

        return hash_hmac(self::HASH_METHOD, "/{$this->route}|{$this->data}", self::$signatureToken);
    }


    /**
     * Generate an authorization token using the public key and the signature
     * @return string
     */
    protected function _generateAuthToken()
    {
        if( empty(self::$publicKey) ) {
            throw new \Exception("Public key is not set", 1);
        }

        if( empty(self::$signatureToken) ) {
            throw new \Exception("Signature token is not set", 1);
        }

        return base64_encode(self::$publicKey .":". $this->_generateSignature() );
    }


    /**
     * Sets the public key property
     * @param string $publicKey
     */
    public static function setPublicKey($publicKey = null)
    {
        self::$publicKey = $publicKey;
    }


    /**
     * Sets the private key property
     * @param string $privateKey
     */
    public static function setPrivateKey($privateKey = null)
    {
        self::$privateKey = $privateKey;
    }


    /**
     * Sets the 'domain' property
     * @param string $domain  format: [subdomain].mopinion.[tld]
     */
    public static function setDomain($domain = null)
    {
        self::$domain = $domain;
        self::_addHeader('domain', self::$domain);
    }


    /**
     * Set the 'signatureToken' property
     * @param [type] $token [description]
     */
    public function setSignatureToken($token = null)
    {
        self::$signatureToken = $token;
    }


    /**
     * Sets the 'route' property
     * @param string $route
     */
    protected function _setRoute($route=null)
    {
        $this->route = $route;
        return $this;
    }


    /**
     * Set the request data
     */
    public function setData($data=null)
    {
        $this->data = $data;
        return $this;
    }


    /**
     * Add a request header to the 'headers' property
     * @param string $key
     * @param string $value
     */
    protected static function _addHeader($key, $value)
    {
        self::$headers[$key] = $value;
    }


    /**
     * Remove a request header from the 'headers' property
     * @param string $key
     */
    protected static function _removeHeader($key)
    {
        if( in_array( $key, array_keys(self::$headers) ) ){
            unset( self::$headers[$key] );
        }
    }


    /**
     * Return the 'headers' property
     * @return [type] [description]
     */
    private function _getHeaders()
    {
        return self::$headers;
    }


    /**
     * Set/Unset the 'limit' request header
     * @param  integer $limit maximum results returned by the API
     */
    public function limit( $limit = 0 )
    {
        $limit = (int) $limit;

        if( empty($limit) ){
            self::_removeHeader('limit');
        }else{
            self::_addHeader('limit', $limit);
        }

    }


    /**
     * Set/Unset the 'page' request header.
     * Together with the 'limit' this allows the API  to calculate the resultset's offset automatically
     * @param  integer $page pagenumber
     */
    public function page( $page = 0 )
    {
        $page = (int) $page;

        if( empty($page) ){

            self::_removeHeader('page');

        }else{
            self::_addHeader('page', $page);
        }
    }

}
