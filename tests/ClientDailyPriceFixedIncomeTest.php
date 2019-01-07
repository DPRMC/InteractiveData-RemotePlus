<?php

namespace DPRMC\InteractiveData\RemotePlusClient\Tests;

use DPRMC\InteractiveData\ClientDailyPriceFixedIncome;
use DPRMC\InteractiveData\RemotePlusClient\Exceptions\UnparsableDateSentToConstructor;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

class ClientDailyPriceFixedIncomeTest extends TestCase {

    protected $invalidUser = 'user';

    protected $invalidPass = 'pass';

    protected $validDate = '2017-07-31';

    protected $validPrices = [ '38259P508' => 919.46 ];

    protected $invalidDate = 'this is not a date';

    protected $validCusips = [ '38259P508' ]; // Google

    protected $invalidCusips = [ '123456789' ];

    /**
     * @test
     */
    public function testConstructor() {
        $client = new ClientDailyPriceFixedIncome( $this->invalidUser, $this->invalidPass, $this->validDate, $this->validCusips, FALSE );
        $this->assertInstanceOf( ClientDailyPriceFixedIncome::class, $client );
    }

    /**
     * @test
     */
    public function testConstructorWithUnparsableDate() {
        $this->expectException( UnparsableDateSentToConstructor::class );
        new ClientDailyPriceFixedIncome( $this->invalidUser, $this->invalidPass, $this->invalidDate, $this->validCusips, FALSE );
    }

    /**
     * @test
     */
    public function testConstructorWithInvalidCusip() {
        $client = new ClientDailyPriceFixedIncome( $this->invalidUser, $this->invalidPass, $this->validDate, $this->invalidCusips, FALSE );
        $this->assertCount( 0, $client->getValidCusips() );
        $this->assertCount( 1, $client->getInvalidCusips() );
    }

    /**
     * @test
     */
    public function testRunWithInvalidCredentials() {
        $this->expectException( ClientException::class );
        $client = new ClientDailyPriceFixedIncome( $this->invalidUser, $this->invalidPass, $this->validDate, $this->validCusips, FALSE );
        $client->run();
    }


    /**
     * @test
     */
    public function testRunWithStubResponse() {
        $client = new ClientDailyPriceFixedIncome( $this->invalidUser, $this->invalidPass, $this->validDate, $this->validCusips, FALSE );

        // Create a stub for the ClientDailyPriceFixedIncome class.
        $stubClient = $this->createPartialMock( ClientDailyPriceFixedIncome::class, [ 'sendRequest',
                                                                                      'getBodyFromResponse' ] );
        // Configure the stub.
        $stubClient->method( 'sendRequest' )
                   ->willReturn( new Response() );

        $fileContents = file_get_contents( './tests/files/38259P508.txt' );
        $stubClient->method( 'getBodyFromResponse' )
                   ->willReturn( $fileContents );

        // Stub CUSIP list
        $refObject   = new ReflectionObject( $stubClient );
        $refProperty = $refObject->getProperty( 'cusips' );
        $refProperty->setAccessible( TRUE );
        $refProperty->setValue( $stubClient, $this->validCusips );
        $cusipPriceArray = $stubClient->run();
        $this->assertArrayHasKey( $this->validCusips[ 0 ], $cusipPriceArray );
        $this->assertEquals( $this->validPrices[ $this->validCusips[ 0 ] ], $cusipPriceArray[ $this->validCusips[ 0 ] ] );
    }
}