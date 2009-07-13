<?php
/**
 * @version     $Id$
 * @category	Koowa
 * @package     Koowa_Chart
 * @subpackage  Google
 * @copyright   Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license     GNU GPL <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.koowa.org
 */

/**
 * Google Chart Helper
 *
 * @author      Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package     Koowa_Chart
 * @subpackage  Google
 * @version     1.0
 */
class KChartGoogleHelper
{


    public static function addArrays($mixed)
    {
        $summedArray = array();

        foreach($mixed as $temp){
            $a=0;
            if(is_array($temp)){
                foreach($temp as $tempSubArray){
                    @$summedArray[$a] += $tempSubArray;
                    $a++;
                }
            }
            else{
                @$summedArray[$a] += $temp;
            }
        }
        return $summedArray;
    }

    public static function getScaledArray($unscaledArray, $scalar)
    {
        $scaledArray = array();

        foreach($unscaledArray as $temp){
            if(is_array($temp)){
                array_push($scaledArray, self::getScaledArray($temp, $scalar));
            }
            else{
                array_push($scaledArray, round($temp * $scalar, 2));
            }
        }
        return $scaledArray;
    }

    public static function getMaxCountOfArray($ArrayToCheck)
    {
        $maxValue = count($ArrayToCheck);

        foreach($ArrayToCheck as $temp){
            if(is_array($temp)){
                $maxValue = max($maxValue, self::getMaxCountOfArray($temp));
            }
        }
        return $maxValue;

    }

    public static function getMaxOfArray($ArrayToCheck){
        $maxValue = 1;

        foreach($ArrayToCheck as $temp){
            if(is_array($temp)){
                $maxValue = max($maxValue, self::getMaxOfArray($temp));
            }
            else{
                $maxValue = max($maxValue, $temp);
            }
        }
        return $maxValue;
    }
}