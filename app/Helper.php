<?php
/**
 * @package zsm-rooster
 * @author     Jeroen Nijhuis <j.nijhuis@epartment.nl>
 * @copyright  2016-2017 Epartment Ecommerce B.V.
 */

namespace App;

class Helper
{
    /**
     * @param $dateString
     * @return \Carbon\Carbon
     */
    static function parseDate($dateString) {
        $month = substr($dateString, 0, 3);
        $days = '';
        if(strlen($dateString) == 7) {
            $days = substr($dateString, 3, 2);
        } else {
            $days = substr($dateString, 3, 1);
        }

        $monthNumber = 0;
        switch ($month) {
            case 'jan':
                $monthNumber = 1;
                break;
            case 'feb':
                $monthNumber = 2;
                break;
            case 'mrt':
                $monthNumber = 3;
                break;
            case 'apr':
                $monthNumber = 4;
                break;
            case 'mei':
                $monthNumber = 5;
                break;
            case 'jun':
                $monthNumber = 6;
                break;
            case 'jul':
                $monthNumber = 7;
                break;
            case 'aug':
                $monthNumber = 8;
                break;
            case 'sep':
                $monthNumber = 9;
                break;
            case 'okt':
                $monthNumber = 10;
                break;
            case 'nov':
                $monthNumber = 11;
                break;
            case 'dec':
                $monthNumber = 12;
                break;
        }

        return \Carbon\Carbon::create(null, $monthNumber, $days);
    }
}