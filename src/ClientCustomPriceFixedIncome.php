<?php

namespace DPRMC\InteractiveData;

use DPRMC\CUSIP;
use DPRMC\InteractiveData\RemotePlusClient\Exceptions\UnparsableDateSentToConstructor;

/**
 * Class ClientCustomPriceFixedIncome
 * @package DPRMC\InteractiveData
 */
class ClientCustomPriceFixedIncome extends RemotePlusClient {

    /**
     * @var string YYYY-mm-dd The date you want pricing for.
     */
    protected $date;

    /**
     * @var array The list of CUSIP identifiers that you want daily pricing on.
     */
    protected $cusips = [];

    /**
     * @var array A list of all of the CUSIPs passed into the constructor that were not valid CUSIPs.
     */
    protected $invalidCusips = [];


    protected $pricesRequested = [];


    public static function instantiate( $user, $pass, $date, $cusips, $debug = FALSE ) {
        return new static( $user, $pass, $date, $cusips, $debug );
    }


    /**
     * ClientCustomPriceFixedIncome constructor.
     * @param $user
     * @param $pass
     * @param $date
     * @param $cusips
     * @param bool $debug
     * @throws \Exception
     */
    public function __construct( $user, $pass, $date, $cusips, $debug = FALSE ) {
        parent::__construct( $user, $pass );
        $this->remotePlusDebug = $debug;
        $this->date            = $this->formatDateForRemotePlus( $date );
        $this->cusips          = $this->pruneInvalidCusips( $cusips );
    }


    /**
     * There are 500 pages of documented data points that you can request from InteractiveData. Instead of coding
     * functions to add each one, it makes more sense to have a generic function available to add points.
     * @see Look at the doc titled: "GuideToData20180413.docx" for documentation on the data points.
     * @param string $dataPointCode
     * @return $this
     */
    public function addDataPointCode( string $dataPointCode ) {
        $this->pricesRequested[] = $dataPointCode;
        return $this;
    }

    /**
     * @return $this
     */
    public function addBestPrice() {
        $this->pricesRequested[] = 'PRC'; // number 16.7
        return $this;
    }

    /**
     * @return $this
     */
    public function addIEBid() {
        $this->pricesRequested[] = 'IEBID'; // number 16.7
        return $this;
    }

    /**
     * @return $this
     */
    public function addIEMid() {
        $this->pricesRequested[] = 'IEMID'; // number 16.7
        return $this;
    }

    /**
     * @return $this
     */
    public function addIEAsk() {
        $this->pricesRequested[] = 'IEASK'; // number 16.7
        return $this;
    }


    /**
     * The Remote Plus API is particular about how dates are formatted.
     *
     * @param string $date A string that can be parsed by PHP's strtotime() function.
     *
     * @return string A date formatted as YYYYMMDD that can be read by Remote Plus.
     * @throws \Exception Only thrown if the user passes some garbage into the constructor.
     */
    protected function formatDateForRemotePlus( $date ) {
        $strTime = strtotime( $date );
        if ( $strTime === FALSE ):
            throw new UnparsableDateSentToConstructor( "We could not parse the date you sent to the constructor: [" . $date . "]" );
        endif;
        $date = date( 'Ymd', $strTime );

        return $date;
    }

    /**
     * We don't want to waste time (or money) getting prices on identifiers that are not valid cusips.
     * Prune out the invalid cusips and save those in the local $invalidCusips property.
     *
     * @param array $cusips A list of cusips passed in by the user in the constructor.
     *
     * @return array A list of cusips pruned of any values that aren't valid cusips.
     */
    protected function pruneInvalidCusips( $cusips ) {
        $validCusips = [];
        foreach ( $cusips as $cusip ):
            if ( CUSIP::isCUSIP( $cusip ) ):
                $validCusips[] = $cusip;
            else:
                $this->invalidCusips[] = $cusip;
            endif;
        endforeach;

        return $validCusips;
    }

    /**
     * The Remote Plus API requires the request body to be formatted in a very specific way.
     * The following body is formatted to pull the prices for a list of CUSIPs from a specific date.
     */
    protected function generateBodyForRequest() {
        $pricesRequested = $this->getPricesRequested();

        $this->requestBody = 'Request=' . urlencode( "GET,(" . implode( ',', $this->cusips ) . "),(" . $pricesRequested . ")," . $this->date ) . "&Done=flag\n";
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getPricesRequested() {
        if ( empty( $this->pricesRequested ) ):
            throw new \Exception( "You have to add at least one price for this request." );
        endif;

        return implode( ',', $this->pricesRequested );
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

    /**
     * Extracted this into it's own function so I can stub and test without
     * having to make a request to the IDC server.
     * @return string
     * @codeCoverageIgnore
     */
    protected function getBodyFromResponse() {
        return (string)$this->response->getBody();
    }

    /**
     * @param $value
     *
     * @return float
     */
    protected function formatValueReturnedFromInteractiveData( $value ) {
        if ( is_numeric( $value ) ):
            return (float)$value;
        endif;

        /**
         * “!NA” not available
         * “!NH” holiday (only applicable to US/Canadian securities)
         * “!NE” not expected (e.g., prices for future dates)
         * “!NR” not reported
         * “!N5” an error code 5000 was returned
         * “!N6” an error code 6000 was returned
         * “!N7” an error code 7000 was returned
         * “!N8” an error code 8000 was returned
         */
        return NULL;
    }
}