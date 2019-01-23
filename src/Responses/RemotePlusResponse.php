<?php

namespace DPRMC\InteractiveData;

/**
 * Class RemotePlusResponse
 * @package DPRMC\InteractiveData
 *
 */
class RemotePlusResponse {

    protected $responses = [];

    public function __construct() {
    }

    public function addResponse( FixedIncomeResponse $response ){
        $this->responses[$response->cusip] = $response;
    }

    public function getResponses(){
        return $this->responses;
    }

    /**
     * @param string $key Right now this will almost always be a CUSIP.
     * @return mixed
     */
    public function getResponseByKey(string $key){
        return $this->responses[$key];
    }


}