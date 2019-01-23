<?php

namespace DPRMC\InteractiveData\CustomClients;

use DPRMC\InteractiveData\FixedIncomeResponse;
use DPRMC\InteractiveData\RemotePlusClient;
use DPRMC\InteractiveData\RemotePlusResponse;

class FixedIncomeBestPriceClient extends RemotePlusClient{

    protected $date;
    protected $cusips;
    protected $pricesRequested = [];

    public function __construct( $user, $pass, $date, $cusips, $debug = FALSE ) {
        parent::__construct( $user, $pass );
        $this->remotePlusDebug = $debug;
        $this->date            = $this->formatDateForRemotePlus( $date );
        $this->cusips          = $this->pruneInvalidCusips( $cusips );
    }

    protected function generateBodyForRequest() {
        $this->requestBody = 'Request=' . urlencode( "GET,(" . implode( ',', $this->cusips ) . "),(PRC)," . $this->date ) . "&Done=flag\n";
    }

    protected function processResponse(): RemotePlusResponse {
        $body = $this->getBodyFromResponse();

        $prices = explode( "\n", $body );
        $prices = array_map( 'trim', $prices );
        $prices = array_filter( $prices );
        array_pop( $prices ); // Remove the CRC check.

        $remotePlusResponse = new RemotePlusResponse();

        foreach ( $this->cusips as $i => $cusip ):
            $fixedIncomeResponse = new FixedIncomeResponse();
            $fixedIncomeResponse->addCusip( $cusip );
            $priceParts = explode( ',', $prices[ $i ] );

            foreach ( $priceParts as $j => $price ):
                $fixedIncomeResponse->addPrice(
                    $this->pricesRequested[ $j ],
                    $this->formatValueReturnedFromInteractiveData( $price )
                );
            endforeach;

            $remotePlusResponse->addResponse( $fixedIncomeResponse );
        endforeach;

        return $remotePlusResponse;
    }


}