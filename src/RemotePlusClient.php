<?php
namespace DPRMC\InteractiveData;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * This is the parent class that all API calls must extend.
 * Class RemotePlusClient
 * @package DPRMC\InteractiveData
 */
abstract class RemotePlusClient {

    /**
     * @var string The base URI for the Remote Plus system.
     */
    protected $baseUri = 'http://rplus.interactivedata.com';

    /**
     * @var string The page (resource) to POST your Remote Plus query.
     */
    protected $page = '/cgi/nph-rplus';

    /**
     * @var string Your username supplied by Interactive Data.
     */
    protected $user = '';

    /**
     * @var string The password assigned to your username from Interactive Data.
     */
    protected $pass = '';

    /**
     * @var \GuzzleHttp\Client The GuzzleHttp client used to POST to the Remote Plus API.
     */
    protected $client;

    /**
     * @var Request; The request to the Remote Plus API
     */
    protected $request;

    /**
     * @var Response The response from the Remote Plus API
     */
    protected $response;

    /**
     * @var string The value required by Remote Plus for authentication.
     */
    protected $authorizationHeaderValue = '';

    /**
     * @var bool A parameter we pass in the request to Remote Plus to enable debugging information to be returned.
     */
    protected $remotePlusDebug = TRUE;

    /**
     * @var float The HTTP version that Remote Plus expects for requests.
     */
    protected $remotePlusHttpVersion = 1.0;

    /**
     * @var string The Content-Type header value that Remote Plus is expecting.
     */
    protected $remotePlusContentType = 'application/x-www-form-urlencoded';


    /**
     * @var string The formatted body of the request being sent to the Remote Plus API.
     */
    protected $requestBody = '';


    /**
     * RemotePlusClient constructor.
     *
     * @param $user string The username given to you by Interactive Data
     * @param $pass string The password for the above username.
     */
    public function __construct( $user, $pass ) {
        $this->user                     = $user;
        $this->pass                     = $pass;
        $this->client                   = new Client( [ 'base_uri' => $this->baseUri ] );
        $this->authorizationHeaderValue = $this->getAuthenticationHeaderValue( $this->user, $this->pass );
    }

    /**
     * Returns the value required by Remote Plus for the Authorization header.
     *
     * @param string $username The username set by Interactive Data
     * @param string $pass     The password assigned by Interactive Data
     *
     * @return string The value needed for the Authorization header.
     */
    protected function getAuthenticationHeaderValue( $username, $pass ) {
        return "Basic " . $this->encodeUserAndPassForBasicAuthentication( $username, $pass );
    }

    /**
     * Encodes the user and pass as required by the Basic Authorization.
     * @see https://en.wikipedia.org/wiki/Basic_access_authentication
     *
     * @param string $username The username set by Interactive Data
     * @param string $pass     The password assigned by Interactive Data
     *
     * @return string The base64 encoded user:pass string.
     */
    protected function encodeUserAndPassForBasicAuthentication( $username, $pass ) {
        return base64_encode( $username . ':' . $pass );
    }

    /**
     * Getter method for the Response object.
     * @return Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function run() {
        $this->sendRequest();

        return $this->processResponse();
    }

    /**
     * Sends the request to Remote Plus, and saves the Response object into our local $response property.
     */
    public function sendRequest() {
        $this->response = $this->client->request( 'POST', $this->page, [ 'debug'   => $this->remotePlusDebug,
                                                                         'version' => $this->remotePlusHttpVersion,
                                                                         'headers' => [ 'Content-Type'  => $this->remotePlusContentType,
                                                                                        'Authorization' => $this->getAuthenticationHeaderValue( $this->user, $this->pass ), ],
                                                                         'body'    => $this->requestBody ] );
    }

    /**
     * It's up to each child class to determine what it does with the results
     * sent back from Remote Plus.
     */
    abstract protected function processResponse();

    /**
     * Sets the $this->requestBody property. Every type of request sent to
     * Remote Plus has a different syntax. It makes sense to force the child
     * classes to implement that code.
     */
    abstract protected function generateBodyForRequest();
}