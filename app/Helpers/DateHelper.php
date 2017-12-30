<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Returns the number of days between start and end
     *
     * @param $start_date string
     * @param $end_date string
     *
     * @return int
     */
    static function getDays($start_date, $end_date)
    {
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date);
        $end_date = Carbon::createFromFormat('Y-m-d', $end_date);

        $start_date = clone $start_date;
        $end_date = clone $end_date;

        $start_date->setTime(0, 0, 0);
        $end_date->setTime(0, 0, 0);

        $days = $start_date->diff($end_date)->days + 1; /*WE HAD TO ADD 1 BECAUSE OF DAYS*/

        return $days;
    }

    /**
     * Returns an array with all the dates from a range
     *
     * @param $start string
     * @param $end string
     * @param $format string
     *
     * @return array
     */
    static function getDatesFromRange($start, $end) {

        $format = 'Y-m-d';

        $array = array();
        $interval = new \DateInterval('P1D');

        $realEnd = new \DateTime($end);

        $realEnd->add($interval);

        $range = new \DatePeriod(new \DateTime($start), $interval, $realEnd);

        foreach($range as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }

    /**
     * Returns an array of times (with an hour interval)from a range
     *
     * @param $start int
     * @param $end int
     *
     * @return array
     */
    static function getEachHourOfDateRange($start, $end) {

        $dates = self::getDatesFromRange($start, $end);

        $times = array();

        foreach ($dates as $date){

            $lower = 0;
            $step = 3600;
            $format = '';

            $upper = 86400-3600;


            if ( empty( $format ) ) {
                $format = $date.'\TH:i';
            }

            foreach ( range( $lower, $upper, $step ) as $increment ) {
                $increment = gmdate( 'H:i', $increment );

                list( $hour, $minutes ) = explode( ':', $increment );

                $date = new \DateTime( $hour . ':' . $minutes );

                $times[] = $date->format( $format );
            }
        }

        return $times;
    }

    /**
     * Returns an array of weekends from a range - may not be needed
     *
     * @param $start string
     * @param $end string
     *
     * @return array
     */
    static function getWeekendsDatesFromRange($start, $end) {


        $start = new \DateTime($start);
        $start = $start->getTimestamp();

        $end = new \DateTime($end);
        $end = $end->getTimestamp();

        $day = intval(date("N", $start));

        $array = array();

        if ($day < 6) {
            $start += (6 - $day) * 86400;
        }

        $i = 0;

        while ($start <= $end) {

            $day = intval(date("N", $start));
            if ($day == 6) {

                $array[$i]['name'] = 'Weekends';
                $array[$i]['start_date'] = date_format(date_create(date("r", $start)), 'Y-m-d');
                $array[$i]['end_date'] = date_format(date_create(date("r", $start+86400)), 'Y-m-d');
                $array[$i]['colour'] = '#0000fd';

                $i = $i +1;

                $start += 86400;

            }
            elseif ($day == 7) {
                $start += 518400; //6 days = 86400 * 6
            }

        }

        return $array;


    }
}