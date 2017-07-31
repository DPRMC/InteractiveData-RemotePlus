# InteractiveData-RemotePlus

[![Build Status](https://travis-ci.org/DPRMC/InteractiveData-RemotePlus.svg?branch=master)](https://travis-ci.org/DPRMC/InteractiveData-RemotePlus)
[![Latest Stable Version](https://poser.pugx.org/dprmc/interactive-data-remote-plus/v/stable)](https://packagist.org/packages/dprmc/interactive-data-remote-plus)
[![Total Downloads](https://poser.pugx.org/dprmc/interactive-data-remote-plus/downloads)](https://packagist.org/packages/dprmc/interactive-data-remote-plus)
[![License](https://poser.pugx.org/dprmc/interactive-data-remote-plus/license)](https://packagist.org/packages/dprmc/interactive-data-remote-plus)
[![composer.lock](https://poser.pugx.org/dprmc/interactive-data-remote-plus/composerlock)](https://packagist.org/packages/dprmc/interactive-data-remote-plus)
[![Coverage Status](https://coveralls.io/repos/github/DPRMC/InteractiveData-RemotePlus/badge.svg?branch=master)](https://coveralls.io/github/DPRMC/InteractiveData-RemotePlus?branch=master)

A php package that interfaces with the Remote Plus pricing HTTP API by Interactive Data.

## Usage
```php
use DPRMC\InteractiveData\ClientDailyPriceFixedIncome;

$date = "2017-07-31";
$cusipsToQuery = [
    '38259P508'
];

$client = new ClientDailyPriceFixedIncome('yourInteractiveDataUser',
                                          'yourInteractiveDataPass',
                                          $date,
                                          $cusipsToQuery);

$response = $client->run();

foreach ($response as $cusip => $price):
    echo "CUSIP $cusip had a price of $price on $date";
endforeach;
```