<?php
namespace DPRMC\InteractiveData;

use DPRMC\CUSIP;
use DPRMC\InteractiveData\RemotePlusClient\Exceptions\UnparsableDateSentToConstructor;

/**
 * Class ClientDailyPriceFixedIncome
 * @package DPRMC\InteractiveData
 */
class ClientDailyPriceFixedIncome extends RemotePlusClient {

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


    /**
     * ClientDailyPriceFixedIncome constructor.
     *
     * @param string $user
     * @param string $pass
     * @param string $date
     * @param array  $cusips
     * @param bool   $debug
     */
    public function __construct( $user, $pass, $date, $cusips, $debug = FALSE ) {
        parent::__construct( $user, $pass );
        $this->remotePlusDebug = $debug;
        $this->date            = $this->formatDateForRemotePlus( $date );
        $this->cusips          = $this->pruneInvalidCusips( $cusips );
        $this->generateBodyForRequest();
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
        $this->requestBody = 'Request=' . urlencode( "GET,(" . implode( ',', $this->cusips ) . "),(PRC)," . $this->date ) . "&Done=flag\n";
    }

    /**
     * @return array
     */
    protected function processResponse() {
        $body = (string)$this->response->getBody();

        $prices = explode( "\n", $body );
        $prices = array_map( 'trim', $prices );

        array_pop( $prices ); // Remove the CRC check.

        $return = [];
        foreach ( $this->cusips as $i => $cusip ) {
            $return[ $cusip ] = $this->formatValueReturnedFromInteractiveData( $prices[ $i ] );
        }

        return $return;
    }

    protected function formatValueReturnedFromInteractiveData( $value ) {
        if ( is_numeric( $value ) ) {
            return (float)$value;
        }

        return str_replace( '"', '', $value );
    }
}