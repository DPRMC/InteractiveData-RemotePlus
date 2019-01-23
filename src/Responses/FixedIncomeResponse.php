<?php

namespace DPRMC\InteractiveData;

class FixedIncomeResponse {

    public $cusip;
    public $prices; // array code => price

    public function __construct() {
    }

    public static function instantiate() {
        return new static();
    }

    public function addCusip( string $cusip ) {
        $this->cusip = $cusip;
        return $this;
    }

    public function addPrice( string $code, float $price = NULL ) {
        $this->prices[ $code ] = $price;
        return $this;
    }

}