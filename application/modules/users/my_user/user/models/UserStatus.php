<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
abstract class TUserStatus
{
    const statusNone=0;
    const statusOnline = 1;
    const statusAway = 2;
    const statusOffline = 3;

    public static function getUserStatusFromDate($dtDate, $TimeLibrary)
    {
        if ($dtDate == null) return TUserStatus::statusOffline;
        else
        {
            $dateCalculate = $TimeLibrary->convertMongoDateToTime($dtDate);
            $dateNow = $TimeLibrary->getDateTimeNowUnix();

            $years = ($dateCalculate->diff($dateNow)->y);
            $months = $dateCalculate->diff($dateNow)->m;
            $days = ($dateCalculate->diff($dateNow)->d);
            $hours = ($dateCalculate->diff($dateNow)->h);

            if (($years > 0)||($months > 0) || ($days > 0)||($hours > 0)) return TUserStatus::statusOffline;
            $minutes = ($dateCalculate->diff($dateNow)->i);
            if ($minutes > 6) return TUserStatus::statusOffline;
            else
            if ($minutes > 2) return TUserStatus::statusAway;
            else
            return TUserStatus::statusOnline;

        }
    }
}