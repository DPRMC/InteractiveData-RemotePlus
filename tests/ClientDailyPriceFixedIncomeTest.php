<?php
namespace DPRMC\InteractiveData\RemotePlusClient\Tests;

use DPRMC\InteractiveData\ClientDailyPriceFixedIncome;
use DPRMC\InteractiveData\RemotePlusClient\Exceptions\UnparsableDateSentToConstructor;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

class ClientDailyPriceFixedIncomeTest extends TestCase {

    protected $invalidUser = 'user';

    protected $invalidPass = 'pass';

    protected $validDate = '2017-07-31';

    protected $invalidDate = 'this is not a date';

    protected $validCusips = [ '38259P508' ]; // Google

    protected $invalidCusips = [ '123456789' ];

    /**
     *
     */
    public function testConstructor() {
        $client = new ClientDailyPriceFixedIncome( $this->invalidUser, $this->invalidPass, $this->validDate, $this->validCusips, FALSE );
        $this->assertInstanceOf( ClientDailyPriceFixedIncome::class, $client );
        $this->assertAttributeCount( 1, 'cusips', $client );
    }

    /**
     *
     */
    public function testConstructorWithUnparsableDate() {
        $this->expectException( UnparsableDateSentToConstructor::class );
        new ClientDailyPriceFixedIncome( $this->invalidUser, $this->invalidPass, $this->invalidDate, $this->validCusips, FALSE );
    }

    /**
     *
     */
    public function testConstructorWithInvalidCusip() {
        $client = new ClientDailyPriceFixedIncome( $this->invalidUser, $this->invalidPass, $this->validDate, $this->invalidCusips, FALSE );
        $this->assertAttributeCount( 0, 'cusips', $client );
        $this->assertAttributeCount( 1, 'invalidCusips', $client );
    }

    /**
     *
     */
    public function testRunWithInvalidCredentials() {
        $this->expectException( ClientException::class );
        $client = new ClientDailyPriceFixedIncome( $this->invalidUser, $this->invalidPass, $this->validDate, $this->validCusips, FALSE );
        $client->run();
    }
}