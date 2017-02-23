<?php
namespace DPRMC\InteractiveData;

use GuzzleHttp\Client;


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

    protected $baseUri = 'rplus.interactivedata.com';
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
    }


    public function request() {
        $this->rawResponse = $this->client->request('POST',
                                                    $this->page,
                                                    ['form_params' => ['Request' => implode(',',
                                                                                            $this->cusips),
                                                                       'Done' => 'request'],
                                                     'auth' => [$this->user,
                                                                $this->pass]


        ]);

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