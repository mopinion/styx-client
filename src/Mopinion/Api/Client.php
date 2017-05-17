<?php

namespace Mopinion\Api;

use Mopinion\Api\BaseClient;
use Mopinion\Api\Response\Response;
use Mopinion\Api\Route;


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
        $this->_setRoute( Route::ROOT );

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

        $this->_setRoute( Route::TOKEN );

        return $this->_makeRequest( Response::MODEL_TOKEN );
    }


    /**
     * Retrieve Account data
     * @return Mopinion\Api\Response\Response
     */
    public function getAccount()
    {
        $this->_setRoute( Route::ACCOUNT );

        return $this->_makeRequest();
    }


    /**
    * Retrieve feedback for given report
    * @return Mopinion\Api\Response\Response
    */
    public function getReportFeedback( $reportId = null )
    {
        $this->_addHeader('X-Report-Id', $reportId);
        $this->_setRoute( Route::REPORT_FEEDBACK );

        return $this->_makeRequest();
    }


    /**
     * Retrieve fields for given report
     * @return Mopinion\Api\Response\Response
     */
    public function getReportFields( $reportId = null )
    {
        $this->_addHeader('X-Report-Id', $reportId);
        $this->_setRoute( Route::REPORT_FIELDS );

        return $this->_makeRequest();
    }


    /**
     * Retrieve feedback for given dataset
     * @return Mopinion\Api\Response\Response
     */
    public function getDatasetFeedback( $datasetId = null )
    {
        $this->_addHeader('X-Dataset-Id', $datasetId);
        $this->_setRoute( Route::DATASET_FEEDBACK );

        return $this->_makeRequest();
    }


    /**
     * Retrieve fields for given dataset
     * @return Mopinion\Api\Response\Response
     */
    public function getDatasetFields( $datasetId = null )
    {
        $this->_addHeader('X-Dataset-Id', $datasetId);
        $this->_setRoute( Route::DATASET_FIELDS );

        return $this->_makeRequest();
    }

}