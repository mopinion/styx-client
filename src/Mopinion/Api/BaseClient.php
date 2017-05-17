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
    const API_VERSION   = '0.0.1';
    const API_URL       = 'https://api.mopinion.com';

    const HASH_METHOD   = 'sha256';
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';

    private $privateKey         = null;
    private $publicKey          = null;
    private $domain             = null;
    private $route              = null;
    private $signatureToken     = null;
    private $data               = '';


    public function __construct($domain = null, $publicKey = null, $privateKey = null)
    {
        if( !empty( $domain ) ) { $this->setDomain( $domain ); }
        if( !empty( $publicKey) ) { $this->setPublicKey( $publicKey );  }
        if( !empty( $privateKey) ) { $this->setPrivateKey( $privateKey );  }
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
            $requestOptions['auth'] = array($this->publicKey, $this->privateKey);

        }else if ($this->route !== Route::ROOT ){

            $this->_addHeader('X-Auth-Token', $this->_generateAuthToken() );
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
        if(empty($this->signatureToken)){
            $code = 1;
            throw new \Exception("Signature token is not set, error code:{$code}");
        }

        // overload signature token for demo settings
        if ($this->privateKey === 'mpn_demo_private'){ return 'mpn_demo_signature'; }

        return hash_hmac(self::HASH_METHOD, "/{$this->route}|{$this->data}", $this->signatureToken);
    }


    /**
     * Generate an authorization token using the public key and the signature
     * @return string
     */
    protected function _generateAuthToken()
    {
        if( empty($this->publicKey) ) {
            throw new \Exception("Public key is not set", 1);
        }

        if( empty($this->signatureToken) ) {
            throw new \Exception("Signature token is not set", 1);
        }

        return base64_encode($this->publicKey .":". $this->_generateSignature() );
    }


    /**
     * Sets the public key property
     * @param string $publicKey
     */
    public function setPublicKey($publicKey = null)
    {
        $this->publicKey = $publicKey;

        return $this;
    }


    /**
     * Sets the private key property
     * @param string $privateKey
     */
    public function setPrivateKey($privateKey = null)
    {
        $this->privateKey = $privateKey;

        return $this;
    }


    /**
     * Sets the 'domain' property
     * @param string $domain  format: [subdomain].mopinion.[tld]
     */
    public function setDomain($domain = null)
    {
        $this->domain = $domain;
        $this->_addHeader('domain', $this->domain);

        return $this;
    }


    /**
     * Set the 'signatureToken' property
     * @param [type] $token [description]
     */
    public function setSignatureToken($token = null)
    {
        $this->signatureToken = $token;
        return $this;
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
    protected function _addHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }


    /**
     * Remove a request header from the 'headers' property
     * @param string $key
     */
    protected function _removeHeader($key)
    {
        if( in_array( $key, array_keys($this->headers) ) ){
            unset( $this->headers[$key] );
        }

        return $this;
    }


    /**
     * Return the 'headers' property
     * @return [type] [description]
     */
    private function _getHeaders()
    {
        return $this->headers;
    }

}
