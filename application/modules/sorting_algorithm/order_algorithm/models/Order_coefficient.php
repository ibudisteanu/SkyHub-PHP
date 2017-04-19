<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_coefficient extends CI_Model
{
    public $object;
    public $iPersonalCoefficient;
    public $iPublicCoefficient;
    public $iHotnessCoefficient;

    public function calculateHotnessCoefficient($mongoDate)
    {
        if ($mongoDate != null) {
            //$this->iHotnessCoefficient = $mongoDate->sec;

            $sign = 1;
            if ($this->iPublicCoefficient < 0) $sign = -1;

            //$this->iHotnessCoefficient = log( max($this->iPublicCoefficient,1),10) + $sign*($mongoDate->sec - strtotime("2016-05-15 00:00:00"))/30500;
            $this->iHotnessCoefficient = $this->iPublicCoefficient + $sign*($mongoDate->sec - strtotime("2016-05-15 00:00:00"))/50500;
        }
    }

    public function toString()
    {
        return '('.$this->iPublicCoefficient.'     '.$this->iPersonalCoefficient.'         '.$this->iHotnessCoefficient.')';
    }

}