<?php

class TimeLibrary
{
    private function getDateTimeNow($sTimeZone='')
    {
        $datetime = new DateTime();

        if ($sTimeZone != '')
        {
            $tz_object = new DateTimeZone($sTimeZone);
            //date_default_timezone_set('Brazil/East');
            $datetime->setTimezone($tz_object);
        }

        return $datetime->format('Y\-m\-d\ h:i:s');
    }

    public function getDateTimeNowUnix($strDateTime = null, $strTimeZone = "Europe/London")
    {
        $objTimeZone = new DateTimeZone($strTimeZone);

        $objDateTime = new DateTime();
        $objDateTime->setTimezone($objTimeZone);

        if (!empty($strDateTime)) {
            $fltUnixTime = (is_string($strDateTime)) ? strtotime($strDateTime) : $strDateTime;

            if (method_exists($objDateTime, "setTimestamp")) {
                $objDateTime->setTimestamp($fltUnixTime);
            }
            else {
                $arrDate = getdate($fltUnixTime);
                $objDateTime->setDate($arrDate['year'], $arrDate['mon'], $arrDate['mday']);
                $objDateTime->setTime($arrDate['hours'], $arrDate['minutes'], $arrDate['seconds']);
            }
        }
        return $objDateTime;
    }

    public function getDateTimeNowUnixMongoDate($strDateTime = null, $strTimeZone = "Europe/London")
    {
        return new MongoDate($this->getDateTimeNowUnix($strDateTime, $strTimeZone)->getTimestamp());
    }

    /*
    public function getDateTimeNowUnixString($strDateTime = null, $strTimeZone = "Europe/London")
    {
        return $this->getDateTimeNowUnix($strDateTime,$strTimeZone)->format("Y-m-d H:i:s");
    }*/

    public function convertMongoDateToTime($date)
    {
        if (is_string($date)) $dateCalculate = new DateTime($date);
        else
            if (get_class($date) == 'MongoDate')
            {
                $dateCalculate = new DateTime();
                $dateCalculate->setTimestamp($date->sec);
            }
            else
                if (get_class($date) == 'DateTime') $dateCalculate = $date;
                else $dateCalculate = new DateTime($date);

        return $dateCalculate;
    }

    public function convertMongoDateToTimeString($date)
    {
        return $this->convertMongoDateToTime($date)->format('Y\-m\-d\ h:i:s');
    }

    public function getTimeDifferenceDateAndNowString($date)
    {
        $dateCalculate = $this->convertMongoDateToTime($date);

        $dateNow = $this->getDateTimeNowUnix();
        $diff = $dateCalculate->diff($dateNow);
        
        /*var_dump($date); echo '<br/>';
        var_dump($dateNow); echo '<br/>';
        var_dump($dateCalculate);*/

        $years = ($diff->y);
        $months = $diff->m;
        $days = ($diff->d);

        $result = '';
        if ($years != 0)  $result .= $years.'y ';
        if ($months != 0) $result .= $months .'m  ';
        if ($days != 0)
        {
            $result .= $days .'d ';
            if (($years == 0) && ($months == 0) && ($days <= 3))
            {
                $hours = ($diff->h);
                if ($hours != 0)  $result .= $hours.'h ';
            }
        }

        if ($result == '')
        {
            $hours = ($diff->h);
            $minutes = ($diff->i);

            if ($hours != 0)  $result .= $hours.'h ';
            if ($minutes != 0) $result .= $minutes .'m ';
            if ($hours <= 1)
            {
                $seconds = ($diff->s);
                if ($seconds != '') $result .= $seconds . 's ';
            }

            if ($result == '')
            {
                $result .= '0 sec';
            }

        }


        return $result . ($diff->invert ? ' ago' : '');
    }

    public function getTimeDifferenceDateAndNowInDays($date)
    {
        $dateCalculate = $this->convertMongoDateToTime($date);

        $dateNow = $this->getDateTimeNowUnix();
        $diff = $dateCalculate->diff($dateNow);

        $years = ($diff->y*30*12);
        $months = $diff->m*30;
        $days = ($diff->d);

        return ($diff->invert ? -1 : 1) * ($years + $months + $days);
    }

    public function getTimeDifferenceDateAndNowInMinutes($date)
    {
        $dateCalculate = $this->convertMongoDateToTime($date);

        $dateNow = $this->getDateTimeNowUnix();
        $diff = $dateCalculate->diff($dateNow);

        $years = ($diff->y*60*24*30*12);
        $months = $diff->m*60*24*30;
        $days = ($diff->d*60*24);
        $hours = ($diff->h*60);
        $minutes = ($diff->i);
        $seconds = (float)($diff->s)/100;

        return ($diff->invert ? -1 : 1) * ($years + $months + $days  + $hours + $minutes + $seconds);
    }


}