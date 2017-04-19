<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'modules/sorting_algorithm/order_algorithm/models/Order_coefficient.php';

class Order_coefficient_sort extends CI_Model
{
    public function __construct()
    {
        $this->load->library('TimeLibrary',null,'TimeLibrary');
    }

    private static function compareDataByPersonalCoefficient($a, $b)
    {
        return ($b->calculateOrderCoefficient()->iPersonalCoefficient - $a->calculateOrderCoefficient()->iPersonalCoefficient);
    }

    private static function compareDataByPublicCoefficient($a, $b)
    {
        return ($b->calculateOrderCoefficient()->iPublicCoefficient - $a->calculateOrderCoefficient()->iPublicCoefficient);
    }

    private static function compareDataByHotnessCoefficient($a, $b)
    {
        return ($b->calculateOrderCoefficient()->iHotnessCoefficient - $a->calculateOrderCoefficient()->iHotnessCoefficient);
    }

    /*  Sorting the categories from this forum*/
    public function sortCoefficientArray($array, $iPageIndex=1, $iCount=10)
    {
        if (count($array)  <= 1) return $array;
        if ($iPageIndex < 1) $iPageIndex = 1;

        $arrayPublic = $array;
        $arrayHotness = $array;
        usort($array, array('Order_coefficient_sort','compareDataByPersonalCoefficient'));
        usort($arrayPublic , array('Order_coefficient_sort','compareDataByPublicCoefficient'));
        usort($arrayHotness, array('Order_coefficient_sort','compareDataByHotnessCoefficient'));

        $arrResult = array();

        for ($index=0; ($index < $iPageIndex*$iCount) && ($index < count($array)); $index++)
        {
            switch ($index % 5)
            {
                case 0:
                case 1:
                    $element = $this->getFirstArrayElement($array);
                    $this->removeArrayElement($arrayPublic,$element);
                    $this->removeArrayElement($arrayHotness,$element);
                    break;
                case 2:
                    $element = $this->getFirstArrayElement($arrayPublic);
                    $this->removeArrayElement($array,$element);

                    $this->removeArrayElement($arrayHotness,$element);
                    break;
                case 3:
                case 4:
                    /*$element = $this->getFirstArrayElement($arrayHotness);
                    $this->removeArrayElement($array,$element);
                    $this->removeArrayElement($arrayPublic,$element);*/
                    $element = $this->getFirstArrayElement($arrayPublic);
                    $this->removeArrayElement($array,$element);
                    $this->removeArrayElement($arrayHotness,$element);
                    break;
            }

            if ( ($iPageIndex-1)*$iCount <= $index )
                array_push($arrResult, $element);
        }

        return $arrResult;
    }

    private function getFirstArrayElement(&$array)
    {
        for ($index=0; $index < count($array); $index++)
            if ($array[$index] != null)
            {
                $element = $array[$index];
                $array[$index]=null;
                return $element;
            }
    }

    private function removeArrayElement(&$array, $element)
    {
        if (is_array($array))
        for ($index=0; $index < count($array); $index++)
            if ($array[$index] === $element)
            {
                $array[$index] = null;
                return;
            }
    }

}