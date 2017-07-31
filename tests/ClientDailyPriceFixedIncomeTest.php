<?php
namespace DPRMC\InteractiveData\RemotePlusClient\Tests;

use DPRMC\InteractiveData\ClientDailyPriceFixedIncome;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

class ClientDailyPriceFixedIncomeTest extends TestCase {

    public function testConstruct() {
        $client = new ClientDailyPriceFixedIncome( 'user', 'pass', '2017-07-31', [ '38259P508' ], FALSE );
        $this->assertInstanceOf( ClientDailyPriceFixedIncome::class, $client );
    }

    public function testRunWithInvalidCredentials() {
        $this->expectException( ClientException::class );
        $client   = new ClientDailyPriceFixedIncome( 'user', 'pass', '2017-07-31', [ '38259P508' ], FALSE );
        $response = $client->run();
    }
}