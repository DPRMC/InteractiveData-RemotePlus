<?php
namespace DPRMC\InteractiveData;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;


class RemotePlusClient {

    /**
     * @var string
     */
    protected $user = '';

    /**
     * @var string
     */
    protected $pass = '';

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \GuzzleHttp\Psr7\Request;
     */
    protected $request;

    protected $baseUri = 'http://rplus.interactivedata.com';
    protected $page = '/cgi/nph-rplus';
    protected $requestPrefix = 'Request=';
    protected $requestSuffix = '&Done=flag';

    protected $rawData = '';
    protected $urlEncodedData = '';

    protected $rawResponse = '';

    protected $cusips = ['004421JM6',
                         '86359DLW5'];


    /**
     * RemotePlusClient constructor.
     * @param $user string The username given to you by Interactive Data
     * @param $pass string The password for the above username.
     */
    public function __construct($user, $pass) {
        $this->setUser($user);
        $this->setPass($pass);
        $this->client = new Client(['base_uri' => $this->baseUri]);

        $body = 'Request=GET%2CIDC%2CDES1&Done=flag';
        $this->request = new Request('POST', $this->baseUri . $this->page, [], $body);

    }


    public function request() {

        $client = new Client(['base_uri' => 'http://rplus.interactivedata.com']);
        $cusips = ['004421JM6',
                   '86359DLW5'];
        $body = "Request=GET%2CIBM%2CDES1&Done=flag\n";
        $body = 'Request=' . urlencode("GET,(" . implode(',',$cusips) . "),(PRC)," . date('Ymd')) . "&Done=flag\n";
        $response = $client->request('POST',
                                     '/cgi/nph-rplus',
                                     ['debug' => true,
                                      'version' => 1.0,
                                      'headers' => [
                                          'Content-Type' => 'application/x-www-form-urlencoded',
                                          'Authorization'     => 'Basic ZDRkcnBrMTpXaW50ZXIxNw==',
                                      ],
                                      'body' => $body]);

        $this->rawResponse = $this->client->send($this->request, ['debug'=>true]);

        return $this->rawResponse;
    }

    public function addCusip($cusip) {

    }

    public function removeCusip($cusip) {

    }

    /**
     * @return string
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getPass() {
        return $this->pass;
    }

    /**
     * @param string $pass
     */
    public function setPass($pass) {
        $this->pass = $pass;
    }

    /**
     * @return string
     */
    public function getApiUrl() {
        return $this->apiUrl;
    }

    /**
     * @param string $apiUrl
     */
    public function setApiUrl($apiUrl) {
        $this->apiUrl = $apiUrl;
    }
}