<?php
namespace DPRMC\InteractiveData\RemotePlusClient\Tests;

use DPRMC\InteractiveData\ClientDailyPriceFixedIncome;
use DPRMC\InteractiveData\RemotePlusClient\Exceptions\UnparsableDateSentToConstructor;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

class ClientDailyPriceFixedIncomeTest extends TestCase {

    public function testConstructor() {
        $client = new ClientDailyPriceFixedIncome( 'user', 'pass', '2017-07-31', [ '38259P508' ], FALSE );
        $this->assertInstanceOf( ClientDailyPriceFixedIncome::class, $client );
    }

    public function testConstructorWithUnparsableDate() {
        $this->expectException( UnparsableDateSentToConstructor::class );
        new ClientDailyPriceFixedIncome( 'user', 'pass', 'this is not a date', [ '38259P508' ], FALSE );
    }

    public function testRunWithInvalidCredentials() {
        $this->expectException( ClientException::class );
        $client   = new ClientDailyPriceFixedIncome( 'user', 'pass', '2017-07-31', [ '38259P508' ], FALSE );
        $response = $client->run();
    }
}