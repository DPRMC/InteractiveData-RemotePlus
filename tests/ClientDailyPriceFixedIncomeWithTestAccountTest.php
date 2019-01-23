<?php

namespace DPRMC\InteractiveData\RemotePlusClient\Tests;

use DPRMC\InteractiveData\ClientCustomPriceFixedIncome;
use DPRMC\InteractiveData\ClientDailyPriceFixedIncome;
use DPRMC\InteractiveData\RemotePlusClient\Exceptions\UnparsableDateSentToConstructor;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

class ClientDailyPriceFixedIncomeWithTestAccountTest extends TestCase {


    /**
     * @test
     * @group custom
     */
    public function testCustomPriceFixedIncome() {

        $user   = $_ENV[ 'ICE_TEST_USER' ];
        $pass   = $_ENV[ 'ICE_TEST_PASS' ];
        $date   = '2018-12-31';
        $cusips = [ '17307GNX2',
                    '07325KAG3',
                    '22541QFF4',
                    '933095AF8',
                    '86358EUD6',
                    '07384YTS5' ];
        $debug  = FALSE;


        $client = ClientCustomPriceFixedIncome::instantiate( $user, $pass, $date, $cusips, $debug )->addIEBid()->addIEMid();

        $result = $client->run();

        print_r( $result );
    }


}